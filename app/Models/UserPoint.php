<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    const TABLE_NAME = 'user_point';
    const FIELD_USER_ID = 'user_id';
    const FIELD_FROM = 'from_date';
    const FIELD_TO = 'to_date';
    const FIELD_COUNT_EASY = 'count_easy';
    const FIELD_COUNT_MID = 'count_mid';
    const FIELD_COUNT_HARD = 'count_hard';
    const FIELD_POINT = 'point';
    const FIELD_IS_PASS = 'is_pass';

    const IS_PASS_YES = 1;
    const IS_PASS_NO = 0;

    const CHANGEABLE_FIELDS = [
        self::FIELD_USER_ID,
        self::FIELD_FROM,
        self::FIELD_TO,
        self::FIELD_COUNT_EASY,
        self::FIELD_COUNT_MID,
        self::FIELD_COUNT_HARD,
        self::FIELD_POINT,
        self::FIELD_IS_PASS,
    ];

    protected $table = self::TABLE_NAME;

    public function setUserId($userId)
    {
        $this->attributes[self::FIELD_USER_ID] = $userId;
        return $this;
    }

    public function setFromDate($from)
    {
        $this->attributes[self::FIELD_FROM] = $from;
        return $this;
    }

    public function setToDate($to)
    {
        $this->attributes[self::FIELD_TO] = $to;
        return $this;
    }

    public function setCountEasy($count)
    {
        $this->attributes[self::FIELD_COUNT_EASY] = $count;
        return $this;
    }

    public function setCountMid($count)
    {
        $this->attributes[self::FIELD_COUNT_MID] = $count;
        return $this;
    }

    public function setCountHard($count)
    {
        $this->attributes[self::FIELD_COUNT_HARD] = $count;
        return $this;
    }

    public function setPoint($point)
    {
        $this->attributes[self::FIELD_POINT] = $point;
        return $this;
    }

    public function setIsPass($isPass)
    {
        $this->attributes[self::FIELD_IS_PASS] = $isPass;
        return $this;
    }

    public function getCountEasy()
    {
        return $this->attributes[self::FIELD_COUNT_EASY];
    }

    public function getCountMid()
    {
        return $this->attributes[self::FIELD_COUNT_MID];
    }

    public function getCountHard()
    {
        return $this->attributes[self::FIELD_COUNT_HARD];
    }

    public function getPoint()
    {
        return $this->attributes[self::FIELD_POINT];
    }

    public function getIsPass()
    {
        return $this->attributes[self::FIELD_IS_PASS];
    }
}
