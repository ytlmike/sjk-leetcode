<?php

namespace App\Console\Commands;

use App\Models\LeetcodeQuestion;
use App\Models\SjkUser;
use App\Models\UserSubmit;
use App\Utils\Leetcode;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncUserQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync User Submit Questions Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $users = SjkUser::all();
        $leetcode = new Leetcode();
        $insert = [];
        foreach ($users as $user) {
            $submissions = $leetcode->getUserSubmissions($user->getSlug());
            foreach ($submissions as $key => $submission) {
                if(UserSubmit::getByUserIdAndSubmitTime($user->getId(), $submission['time'])){
                    unset($submissions[$key]);
                }
            }
            $frontIds = array_column($submissions, 'front_id');
            $questionIds = LeetcodeQuestion::frontIds2QuestionIds($frontIds);
            foreach ($submissions as $submission) {
                /** @var Carbon $time */
                $time = $submission['time'];
                $insert[] = [
                    UserSubmit::FIELD_USER_ID => $user->getId(),
                    UserSubmit::FIELD_QUESTION_ID => $questionIds[$submission['front_id']],
                    UserSubmit::FIELD_SUBMIT_AT => $time->toDateTimeString(),
                    UserSubmit::FIELD_LANGUAGE => $submission['language'],
                    UserSubmit::FIELD_RESULT => $submission['result']
                ];
            }
        }
        UserSubmit::saveAll($insert);
    }
}
