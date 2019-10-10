<?php


namespace App\Models;

use Illuminate\Support\Arr;

class UserSubmit extends BaseModel
{
    const TABLE_NAME = 'user_submit';
    const FIELD_USER_ID = 'user_id';
    const FIELD_QUESTION_ID = 'question_id';
    const FIELD_SUBMIT_AT = 'submit_at';
    const FIELD_LANGUAGE = 'language';
    const FIELD_RESULT = 'result';

    protected $table = self::TABLE_NAME;
    protected $fillable = [
        self::FIELD_USER_ID,
        self::FIELD_QUESTION_ID,
        self::FIELD_SUBMIT_AT,
        self::FIELD_LANGUAGE,
        self::FIELD_RESULT
    ];

    const LANGUAGE_MAP = [];

    const RESULT_MAP = [];

    public function getUserId()
    {
        return $this->attributes[self::FIELD_USER_ID];
    }

    public function setUserId($id)
    {
        $this->attributes[self::FIELD_USER_ID] = $id;
        return $this;
    }

    public function getQuestionId()
    {
        return $this->attributes[self::FIELD_QUESTION_ID];
    }

    public function setQuestionId($id)
    {
        $this->attributes[self::FIELD_QUESTION_ID] = $id;
        return $this;
    }

    public function getSubmitTime()
    {
        return new \DateTime($this->attributes[self::FIELD_SUBMIT_AT]);
    }

    public function setSubmitTime($time)
    {
        if($time instanceof \DateTime){
            $time = $time->format('Y-m-d H:i:s');
        }
        $this->attributes[self::FIELD_SUBMIT_AT] = $time;
    }

    public function getLanguage()
    {
        $language = $this->attributes[self::FIELD_LANGUAGE];
        return Arr::get(self::LANGUAGE_MAP, $language, $language);
    }

    public function setLanguage($language)
    {
        $map = array_flip(self::LANGUAGE_MAP);
        $this->attributes[self::FIELD_LANGUAGE] = Arr::get($map, $language, $language);
    }

    public function getResult()
    {
        $result = $this->attributes[self::FIELD_RESULT];
        return Arr::get(self::RESULT_MAP, $result, $result);
    }

    public function setResult($result)
    {
        $map = array_flip(self::RESULT_MAP);
        $this->attributes[self::FIELD_RESULT] = Arr::get($map, $result, $result);
    }

    public function user()
    {
        return $this->belongsTo(SjkUser::class, self::FIELD_USER_ID);
    }

    public function question()
    {
        return $this->belongsTo(LeetcodeQuestion::class, self::FIELD_QUESTION_ID);
    }
}
