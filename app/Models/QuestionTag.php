<?php


namespace App\Models;

class QuestionTag extends BaseModel
{
    const TABLE_NAME = 'question_tag';

    const FIELD_TAG_NAME = 'tag_name';
    const FIELD_TRANSLATED_NAME = 'translated_name';

    protected $table = self::TABLE_NAME;
    protected $fillable = [
        self::FIELD_TAG_NAME,
        self::FIELD_TRANSLATED_NAME
    ];

    /**
     * @param $name
     * @return self
     */
    public static function getByName($name)
    {
        return self::where(self::FIELD_TAG_NAME, $name)->first();
    }

    public function getName()
    {
        return $this->attributes[self::FIELD_TAG_NAME];
    }

    public function setName($name)
    {
        $this->attributes[self::FIELD_TAG_NAME] = $name;
        return $this;
    }

    public function getTranslatedName()
    {
        return $this->attributes[self::FIELD_TRANSLATED_NAME];
    }

    public function setTranslatedName($name)
    {
        $this->attributes[self::FIELD_TRANSLATED_NAME] = $name;
        return $this;
    }

    public function questions()
    {
        return $this->belongsToMany(
            LeetcodeQuestion::class,
            QuestionHasTag::TABLE_NAME,
            QuestionHasTag::FIELD_TAG_ID,
            QuestionHasTag::FIELD_QUESTION_ID
        );
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
