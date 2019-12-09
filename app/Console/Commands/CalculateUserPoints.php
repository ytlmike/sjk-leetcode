<?php

namespace App\Console\Commands;

use App\Models\LeetcodeQuestion;
use App\Models\SjkUser;
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
        $result = (new Leetcode())->calcUserPoint($users, $start, $end);
        foreach ($result as $item) {
            $user = $item['user'];
            $point = $item['point'];
            $questionCount = $item['counts'];
            print_r("{$user->getName()}，积分：{$point}，简单题{$questionCount[LeetcodeQuestion::DIFFICULTY_LEVEL_EASY]}道，中等题{$questionCount[LeetcodeQuestion::DIFFICULTY_LEVEL_MID]}道，困难题{$questionCount[LeetcodeQuestion::DIFFICULTY_LEVEL_HARD]}道 \n");
        }
    }
}
