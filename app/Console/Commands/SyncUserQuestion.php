<?php

namespace App\Console\Commands;

use App\Models\LeetcodeQuestion;
use App\Models\User;
use App\Models\UserSubmit;
use App\Services\UserSubmitService;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $users = User::all();
        $service = new UserSubmitService();
        foreach ($users as $user) {
            $service->syncUserRecentSubmissions($user);
            sleep(1);
        }
    }
}
