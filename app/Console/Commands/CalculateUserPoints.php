<?php

namespace App\Console\Commands;

use App\Models\LeetcodeQuestion;
use App\Models\SjkUser;
use App\Models\UserSubmit;
use App\Services\Leetcode;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateUserPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:calc_point {start} {end}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = Carbon::parse($this->argument('start'));
        $end = Carbon::parse($this->argument('end'));
        $users = SjkUser::all();
        $users->each(function ($user) use ($start, $end) {
            /** @var SjkUser $user */
            $correctSubmissions = UserSubmit::getCorrectSubmissionInDuration($user->getId(), $start, $end);
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
                    $point += Leetcode::POINT_MAP[$difficultyLevel];
                    $questionIds[] = $submission->getQuestionId();
                }
            });
            print_r("{$user->getName()}，积分：{$point}，简单题{$questionCont[LeetcodeQuestion::DIFFICULTY_LEVEL_EASY]}道，中等题{$questionCont[LeetcodeQuestion::DIFFICULTY_LEVEL_MID]}道，困难题{$questionCont[LeetcodeQuestion::DIFFICULTY_LEVEL_HARD]}道 \n");
        });
    }
}
