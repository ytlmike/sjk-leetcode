<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPoint;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class JudgeUser
 * 每周更新用户的通过情况
 * @package App\Console\Commands
 */
class JudgeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:judge';

    public function handle()
    {
        User::with(['points' => function ($query) {
            $query->where(UserPoint::FIELD_FROM, Carbon::now()->subDays(date('w') - 1)->subWeek()->format('Y-m-d'));
        }])->get()->each(function (User $user) {
            $currentStatus = $user->getStatus();
            /** @var UserPoint $thisWeekPoint */
            $thisWeekPoint = $user->points->first();
            $newStatus = $thisWeekPoint ? $thisWeekPoint->getIsPass() : 0;
            if ($currentStatus != $newStatus) {
                $user->setStatus($newStatus)->save();
            }
        });
    }
}
