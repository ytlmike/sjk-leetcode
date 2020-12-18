<?php


namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserPoint;
use App\Resources\RankUserCollection;
use Carbon\Carbon;

class RankController extends Controller
{
    public function normal()
    {
        $users = User::with(['points' => function ($query) {
            $query->where(UserPoint::FIELD_FROM, Carbon::now()->subDays((date('w') == 0 ? 7 : date('w')) - 1)->format('Y-m-d'));
        }])->where(User::FIELD_STATUS, User::STATUS_NORMAL)
            ->get();

        return $this->successWithData(new RankUserCollection($users));
    }

    public function abnormal()
    {
        $users = User::with(['points' => function ($query) {
            $query->where(UserPoint::FIELD_FROM, Carbon::now()->subDays((date('w') == 0 ? 7 : date('w')) - 1)->format('Y-m-d'));
        }])->where(User::FIELD_STATUS, User::STATUS_BLACK)
            ->get();

        return $this->successWithData(new RankUserCollection($users));
    }
}
