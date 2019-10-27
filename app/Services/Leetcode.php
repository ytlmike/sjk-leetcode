<?php


namespace App\Services;


use App\Models\BaseModel;
use App\Models\LeetcodeQuestion;
use App\Models\QuestionHasTag;
use App\Models\QuestionTag;
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
