<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Collection;

class LeetcodeQuestion extends BaseModel
{
    const TABLE_NAME = 'leetcode_question';

    const FIELD_LEETCODE_ID = 'leetcode_id';
    const FIELD_QUESTION_NAME = 'question_name';
    const FIELD_QUESTION_SLUG = 'question_slug';
    const FIELD_TRANSLATED_NAME = 'translated_name';
    const FIELD_DIFFICULTY_LEVEL = 'difficulty_level';
    const FIELD_FRONT_ID = 'front_id';

    const DIFFICULTY_LEVEL_EASY = 1;
    const DIFFICULTY_LEVEL_MID = 2;
    const DIFFICULTY_LEVEL_HARD = 3;

    protected $table = self::TABLE_NAME;
    protected $fillable = [
        self::FIELD_LEETCODE_ID,
        self::FIELD_QUESTION_NAME,
        self::FIELD_QUESTION_SLUG,
        self::FIELD_TRANSLATED_NAME,
        self::FIELD_DIFFICULTY_LEVEL,
        self::FIELD_FRONT_ID
    ];

    /**
     * @param $id
     * @return self
     */
    public static function getByLeetcodeId($id)
    {
        return self::where(self::FIELD_LEETCODE_ID, $id)->first();
    }

    /**
     * @param $ids
     * @return Collection
     */
    public static function getListByLeetcodeId($ids)
    {
        return self::whereIn(self::FIELD_LEETCODE_ID, $ids)->get();
    }

    public static function frontIds2QuestionIds($frontIds)
    {
        $questions = self::whereIn(self::FIELD_FRONT_ID, $frontIds)->get();
        $map = [];
        $questions->each(function($question) use (&$map) {
            /** @var LeetcodeQuestion $question */
            $map[$question->getFrontId()] = $question->getKey();
        });
        return $map;
    }

    public function getLeetcodeId()
    {
        return $this->attributes[self::FIELD_LEETCODE_ID];
    }

    public function setLeetcodeId($id)
    {
        $this->attributes[self::FIELD_LEETCODE_ID] = $id;
        return $this;
    }

    public function getTitle()
    {
        return $this->attributes[self::FIELD_QUESTION_NAME];
    }

    public function setTitle($title)
    {
        $this->attributes[self::FIELD_QUESTION_NAME] = $title;
        return $this;
    }

    public function getSlug()
    {
        return $this->attributes[self::FIELD_QUESTION_SLUG];
    }

    public function setSlug($slug)
    {
        $this->attributes[self::FIELD_QUESTION_SLUG] = $slug;
        return $this;
    }

    public function getTranslatedTitle()
    {
        return $this->attributes[self::FIELD_TRANSLATED_NAME];
    }

    public function setTranslatedTitle($name)
    {
        $this->attributes[self::FIELD_TRANSLATED_NAME] = $name;
        return $this;
    }

    public function getDifficultyLevel()
    {
        return $this->attributes[self::FIELD_DIFFICULTY_LEVEL];
    }

    public function setDifficultyLevel($level)
    {
        $this->attributes[self::FIELD_DIFFICULTY_LEVEL] = $level;
        return $this;
    }

    public function getFrontId()
    {
        return $this->attributes[self::FIELD_FRONT_ID];
    }

    public function setFrontId($frontId)
    {
        $this->attributes[self::FIELD_FRONT_ID] = $frontId;
        return $this;
    }

    public function tags()
    {
        return $this->belongsToMany(
            QuestionTag::class,
            QuestionHasTag::TABLE_NAME,
            QuestionHasTag::FIELD_QUESTION_ID,
            QuestionHasTag::FIELD_TAG_ID
        );
    }

    public function submits()
    {
        return $this->hasMany(UserSubmit::class, UserSubmit::FIELD_QUESTION_ID);
    }
}
