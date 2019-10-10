<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Collection;

class LeetcodeQuestion extends BaseModel
{
    const TABLE_NAME = 'leetcode_question';

    const FIELD_LEETCODE_ID = 'leetcode_id';
    const FIELD_QUESTION_TITLE = 'question_title';
    const FIELD_QUESTION_SLUG = 'question_slug';
    const FIELD_TRANSLATED_TITLE = 'translated_title';
    const FIELD_DIFFICULTY_LEVEL = 'difficulty_level';

    const DIFFICULTY_LEVEL_EASY = 1;
    const DIFFICULTY_LEVEL_MID = 2;
    const DIFFICULTY_LEVEL_HARD = 3;

    protected $table = self::TABLE_NAME;
    protected $fillable = [
        self::FIELD_LEETCODE_ID,
        self::FIELD_QUESTION_TITLE,
        self::FIELD_QUESTION_SLUG,
        self::FIELD_TRANSLATED_TITLE,
        self::FIELD_DIFFICULTY_LEVEL
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
        return self::where(self::FIELD_LEETCODE_ID, $ids)->get();
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
        return $this->attributes[self::FIELD_QUESTION_TITLE];
    }

    public function setTitle($title)
    {
        $this->attributes[self::FIELD_QUESTION_TITLE] = $title;
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
        return $this->attributes[self::FIELD_TRANSLATED_TITLE];
    }

    public function setTranslatedTitle($name)
    {
        $this->attributes[self::FIELD_TRANSLATED_TITLE] = $name;
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

    public static function saveAll($data)
    {
        $data = array_map(function ($item) {
            $item[self::FIELD_CREATED_AT] = date('Y-m-d H:i:s');
            $item[self::FIELD_UPDATED_AT] = date('Y-m-d H:i:s');
            return $item;
        }, $data);
        return self::insert($data);
    }
}
