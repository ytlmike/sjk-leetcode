<?php


namespace App\Services;


use App\Models\LeetcodeQuestion;
use App\Models\QuestionHasTag;
use App\Models\QuestionTag;
use Illuminate\Support\Facades\DB;

class Leetcode
{
    protected $questionModel;
    protected $tagModel;
    protected $leetcode;

    public function __construct()
    {
        $this->questionModel = new LeetcodeQuestion();
        $this->tagModel = new QuestionTag();
        $this->leetcode = new \App\Utils\Leetcode();
    }

    public function saveQuestions()
    {
        $questions = $this->leetcode->getQuestions();
        $insert = array_map(function ($question){
            return [
                LeetcodeQuestion::FIELD_DIFFICULTY_LEVEL => $question['difficulty'],
                LeetcodeQuestion::FIELD_LEETCODE_ID => $question['id'],
                LeetcodeQuestion::FIELD_QUESTION_SLUG => $question['slug'],
                LeetcodeQuestion::FIELD_QUESTION_TITLE => $question['title'],
                LeetcodeQuestion::FIELD_TRANSLATED_TITLE => $question['translation'],
            ];
        }, $questions);
        $exec = "TRUNCATE TABLE " . LeetcodeQuestion::TABLE_NAME;
        DB::exec($exec);
        LeetcodeQuestion::saveAll($insert);
    }

    public function saveTags()
    {
        $tags = $this->leetcode->getTags();
        $insert = array_map(function ($tag){
            return [
                QuestionTag::FIELD_TAG_NAME => $tag['name'],
                QuestionTag::FIELD_TRANSLATED_NAME => $tag['translatedName'],
            ];
        }, $tags);
        $exec = "TRUNCATE TABLE " . QuestionTag::TABLE_NAME;
        DB::exec($exec);
        QuestionTag::saveAll($insert);

        $exec = "TRUNCATE TABLE " . QuestionHasTag::TABLE_NAME;
        DB::exec($exec);
        foreach ($tags as $tag) {
            $questions = LeetcodeQuestion::getListByLeetcodeId($tag['questions']);
            QuestionTag::getByName($tag['name'])->questions()->saveMany($questions);
        }
    }
}
