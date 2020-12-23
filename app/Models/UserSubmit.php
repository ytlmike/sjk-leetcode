<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class UserSubmit extends BaseModel
{
    const TABLE_NAME = 'user_submit';
    const FIELD_SUBMISSION_ID = 'submission_id';
    const FIELD_USER_ID = 'user_id';
    const FIELD_QUESTION_ID = 'question_id';
    const FIELD_SUBMIT_AT = 'submit_at';
    const FIELD_LANGUAGE = 'language';
    const FIELD_RESULT = 'result';

    const SEARCH_FIELDS = [
        self::FIELD_USER_ID
    ];

    protected $fillable = [
        self::FIELD_USER_ID,
        self::FIELD_QUESTION_ID,
        self::FIELD_SUBMIT_AT,
        self::FIELD_LANGUAGE,
        self::FIELD_RESULT
    ];

    const LANGUAGE_MAP = [
        'A_19'  => 'PHP',
        'A_8'   => 'Bash',
        'A_11'  => 'Python3',
        'A_10'  => 'Go',
        'A_6'   => 'JavaScript',
        'A_3'   => 'MySQL',
        'A_1'   => 'Java',
        'A_4'   => 'C',
    ];

    const RESULT_CORRECT    = 'A_10';
    const RESULT_WRONG      = 'A_11';
    const RESULT_TIMEOUT    = 'A_14';
    const RESULT_EXCEPTION  = 'A_15';

    const RESULT_MAP = [
        self::RESULT_CORRECT    => '通过',
        self::RESULT_WRONG      => '解答错误',
        self::RESULT_TIMEOUT    => '超出时间限制',
        self::RESULT_EXCEPTION  => '运行出错'
    ];

    protected $table = self::TABLE_NAME;

    public static function getByUserIdAndSubmitTime($userId, Carbon $time)
    {
        return self::where(self::FIELD_USER_ID, $userId)->where(self::FIELD_SUBMIT_AT, $time->toDateTimeString())->first();
    }

    /**
     * 查询一个用户指定时间段内的正确提交
     * @param $userId
     * @param $from
     * @param $to
     * @return UserSubmit[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public static function getCorrectSubmissionInDuration($userId, $from, $to)
    {
        return self::with('question')
            ->where(self::FIELD_USER_ID, $userId)
            ->where(self::FIELD_RESULT, self::RESULT_CORRECT)
            ->whereBetween(self::FIELD_SUBMIT_AT, [$from, $to])
            ->get();
    }

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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return LeetcodeQuestion
     */
    public function getQuestion()
    {
        return $this->question;
    }

    public function user()
    {
        return $this->belongsTo(User::class, self::FIELD_USER_ID);
    }

    public function question()
    {
        return $this->belongsTo(LeetcodeQuestion::class, self::FIELD_QUESTION_ID);
    }
}
