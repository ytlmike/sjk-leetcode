<?php


namespace App\Services;


use App\Models\LeetcodeQuestion;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\UserSubmit;
use App\Traits\CurdTrait;
use Carbon\Carbon;

class UserPointService
{
    use CurdTrait;

    protected $entityClass = UserPoint::class;

    public function calcUserPoint(User $user)
    {
        $thisMonday = Carbon::now()->subDays((date('w') == 0 ? 7 : date('w')) - 1)->format('Y-m-d');
        $thisSunday = Carbon::now()->addDays(7 - (date('w') == 0 ? 7 : date('w')))->format('Y-m-d');
        $nextMonday = Carbon::now()->subDays((date('w') == 0 ? 7 : date('w')) - 1)->addWeek()->format('Y-m-d');

        $correctSubmissions = UserSubmit::getCorrectSubmissionInDuration($user->getKey(), $thisMonday, $nextMonday);
        $questionIds = [];
        $questionCont = [
            LeetcodeQuestion::DIFFICULTY_LEVEL_EASY => 0,
            LeetcodeQuestion::DIFFICULTY_LEVEL_MID  => 0,
            LeetcodeQuestion::DIFFICULTY_LEVEL_HARD => 0
        ];
        $point = 0;
        $correctSubmissions->each(function ($submission) use (&$questionCont, &$point, &$questionIds) {
            /** @var UserSubmit $submission */

            if(!in_array($submission->getQuestionId(), $questionIds)){
                $difficultyLevel = $submission->getQuestion()->getDifficultyLevel();
                $questionCont[$difficultyLevel]++;
                $point += LeetcodeService::POINT_MAP[$difficultyLevel];
                $questionIds[] = $submission->getQuestionId();
            }
        });

        $isPass = $this->judgePass($user, $point);
        $this->updateOrCreate([
            UserPoint::FIELD_USER_ID => $user->getKey(),
            UserPoint::FIELD_FROM => $thisMonday
        ], [
            UserPoint::FIELD_TO => $thisSunday,
            UserPoint::FIELD_COUNT_EASY => $questionCont[LeetcodeQuestion::DIFFICULTY_LEVEL_EASY],
            UserPoint::FIELD_COUNT_MID => $questionCont[LeetcodeQuestion::DIFFICULTY_LEVEL_MID],
            UserPoint::FIELD_COUNT_HARD => $questionCont[LeetcodeQuestion::DIFFICULTY_LEVEL_HARD],
            UserPoint::FIELD_POINT => $point,
            UserPoint::FIELD_IS_PASS => $isPass,
        ]);

        /** 本次检查逃出小黑屋时更新 */
        if ($user->getStatus() != $isPass && $isPass == UserPoint::IS_PASS_YES) {
            $user->setStatus($isPass)->save();
        }
    }

    public function judgePass(User $user, $point)
    {
        $line = $user->getStatus() == User::STATUS_BLACK ? 2 * config('app.week_point_line') : config('app.week_point_line');
        return $point >= $line ? UserPoint::IS_PASS_YES : UserPoint::IS_PASS_NO;
    }
}
