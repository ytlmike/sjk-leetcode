<?php


namespace App\Services;


use App\Models\LeetcodeQuestion;
use App\Models\User;
use App\Models\UserSubmit;
use App\Traits\CurdTrait;
use App\Utils\Leetcode;

class UserSubmitService
{
    use CurdTrait;

    protected $entityClass = UserSubmit::class;

    public function syncUserRecentSubmissions(User $user)
    {
        $leetcode = new Leetcode();
        $submissions = $leetcode->getUserSubmissions($user->getSlug());

        if (empty($submissions)) {
            return;
        }

        $existingSubmissionIds = UserSubmit::query()->whereIn(UserSubmit::FIELD_SUBMISSION_ID, array_column($submissions, 'submission_id'))->pluck(UserSubmit::FIELD_SUBMISSION_ID)->toArray();

        foreach ($submissions as $key => $submission) {
            if(in_array($submission['submission_id'], $existingSubmissionIds)){
                unset($submissions[$key]);
            }
        }
        $frontIds = array_column($submissions, 'front_id');
        $questionIds = LeetcodeQuestion::frontIds2QuestionIds($frontIds);
        $insert = [];
        foreach ($submissions as $submission) {
            $insert[] = [
                UserSubmit::FIELD_SUBMISSION_ID => $submission['submission_id'],
                UserSubmit::FIELD_USER_ID => $user->getKey(),
                UserSubmit::FIELD_QUESTION_ID => $questionIds[$submission['front_id']],
                UserSubmit::FIELD_SUBMIT_AT => $submission['time'],
                UserSubmit::FIELD_LANGUAGE => $submission['language'],
                UserSubmit::FIELD_RESULT => $submission['result']
            ];
        }

        UserSubmit::saveAll($insert);
        (new UserPointService())->calcUserPoint($user);
    }
}
