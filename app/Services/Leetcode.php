<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\LeetcodeQuestion;
use App\Models\QuestionHasTag;
use App\Models\QuestionTag;
use App\Models\SjkUser;
use App\Models\UserSubmit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Leetcode
{
    protected $questionModel;
    protected $tagModel;
    protected $leetcode;

    const POINT_MAP = [
        LeetcodeQuestion::DIFFICULTY_LEVEL_EASY => 1,
        LeetcodeQuestion::DIFFICULTY_LEVEL_MID  => 2,
        LeetcodeQuestion::DIFFICULTY_LEVEL_HARD => 4
    ];

    public function __construct()
    {
        $this->questionModel = new LeetcodeQuestion();
        $this->tagModel = new QuestionTag();
        $this->leetcode = new \App\Utils\Leetcode();
    }

    /**
     * 同步用户提交记录
     * @param $users
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncUserSubmit($users)
    {
        if(!is_array($users) && !($users instanceof Collection)){
            $users = [$users];
        }
        $insert = [];
        $leetcode = new \App\Utils\Leetcode();
        foreach ($users as $user) {
            try{
                $submissions = $leetcode->getUserSubmissions($user->getSlug());
            }catch (\Exception $exception){
                $this->saveQuestions();
                $this->saveTags();
                $this->syncUserSubmit($users);
            }
            foreach ($submissions as $key => $submission) {
                if(UserSubmit::getByUserIdAndSubmitTime($user->getId(), $submission['time'])){
                    unset($submissions[$key]);
                }
            }
            $frontIds = array_column($submissions, 'front_id');
            $questionIds = LeetcodeQuestion::frontIds2QuestionIds($frontIds);
            foreach ($submissions as $submission) {
                /** @var Carbon $time */
                $time = $submission['time'];
                $insert[] = [
                    UserSubmit::FIELD_USER_ID => $user->getId(),
                    UserSubmit::FIELD_QUESTION_ID => $questionIds[$submission['front_id']],
                    UserSubmit::FIELD_SUBMIT_AT => $time->toDateTimeString(),
                    UserSubmit::FIELD_LANGUAGE => $submission['language'],
                    UserSubmit::FIELD_RESULT => $submission['result']
                ];
            }
        }
        UserSubmit::saveAll($insert);
    }

    /**
     * 获取用户分数
     * @param $users
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function calcUserPoint($users, Carbon $start, Carbon $end)
    {
        if(!is_array($users) && !($users instanceof Collection)){
            $users = [$users];
        }
        $result = [];
        /** @var SjkUser $user */
        foreach ($users as $user) {
            $correctSubmissions = UserSubmit::getCorrectSubmissionInDuration($user->getId(), $start, $end);
            $questionIds = [];
            $questionCount = [
                LeetcodeQuestion::DIFFICULTY_LEVEL_EASY => 0,
                LeetcodeQuestion::DIFFICULTY_LEVEL_MID  => 0,
                LeetcodeQuestion::DIFFICULTY_LEVEL_HARD => 0
            ];
            $point = 0;
            $correctSubmissions->each(function ($submission) use (&$questionCount, &$point, &$questionIds) {
                /** @var UserSubmit $submission */
                if(!in_array($submission->getQuestionId(), $questionIds)){
                    $difficultyLevel = $submission->getQuestion()->getDifficultyLevel();
                    $questionCount[$difficultyLevel]++;
                    $point += Leetcode::POINT_MAP[$difficultyLevel];
                    $questionIds[] = $submission->getQuestionId();
                }
            });
            $result[] = [
                'user' => $user,
                'point' => $point,
                'counts' => $questionCount,
                'submissions' => $correctSubmissions
            ];
        }
        return $result;
    }

    public function saveQuestions()
    {
        $questions = $this->leetcode->getQuestions();
        $map = [
            LeetcodeQuestion::FIELD_DIFFICULTY_LEVEL => 'difficulty',
            LeetcodeQuestion::FIELD_LEETCODE_ID => 'id',
            LeetcodeQuestion::FIELD_QUESTION_SLUG => 'slug',
            LeetcodeQuestion::FIELD_QUESTION_NAME => 'title',
            LeetcodeQuestion::FIELD_TRANSLATED_NAME => 'translation',
            LeetcodeQuestion::FIELD_FRONT_ID => 'front_id'
        ];
        $this->updateData(new LeetcodeQuestion(), $questions, $map, LeetcodeQuestion::FIELD_FRONT_ID);
    }

    public function saveTags()
    {
        $tags = $this->leetcode->getTags();
        $insert = array_map(function ($tag){
            return [
                QuestionTag::FIELD_TAG_NAME => $tag['name'],
                QuestionTag::FIELD_TRANSLATED_NAME => $tag['translatedName'] ?: '',
            ];
        }, $tags);
        $exec = "TRUNCATE TABLE " . QuestionTag::TABLE_NAME;
        DB::statement($exec);
        QuestionTag::saveAll($insert);

        $exec = "TRUNCATE TABLE " . QuestionHasTag::TABLE_NAME;
        DB::statement($exec);
        foreach ($tags as $tag) {
            $questions = LeetcodeQuestion::getListByLeetcodeId($tag['questions']);
            QuestionTag::getByName($tag['name'])->questions()->saveMany($questions);
        }
    }

    public function updateData(BaseModel $model, array $updateData,  array $map, string $identifyField = BaseModel::FIELD_ID)
    {
        $identify = array_column($updateData, $map[$identifyField]);
        $exists = $model->whereIn($identifyField, $identify)->get($identifyField)->toArray();
        $exists = array_column($exists, $identifyField);
        $insert = [];
        foreach ($updateData as $item) {
            if(!in_array($item[$map[$identifyField]], $exists)){
                $current = [];
                foreach ($map as $field => $key) {
                    $current[$field] = $item[$key];
                }
                $insert[] = $current;
            }
        }
        if(!empty($insert)){
            $model->saveAll($insert);
        }
    }
}
