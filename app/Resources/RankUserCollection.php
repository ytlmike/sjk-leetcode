<?php


namespace App\Resources;


use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RankUserCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $list = [];
        /** @var Collection $users */
        $users = $this->resource;

        $users->each(function (User $user) use (&$list) {
            /** @var UserPoint|null $point */
            $point = $user->points->count() > 0 ? $user->points->first() : null;
            $countEasy = $point ? $point->getCountEasy() : 0;
            $countMid = $point ? $point->getCountMid() : 0;
            $countHard = $point ? $point->getCountHard() : 0;
            $list[] = [
                'slug' => $user->getSlug(),
                'name' => $user->getName(),
                'avatar' => $user->getAvatar(),
                'count_easy' => $countEasy,
                'count_mid' => $countMid,
                'count_hard' => $countHard,
                'point' => $point ? $point->getPoint() : 0,
                'is_pass' => $point ? $point->getIsPass() : UserPoint::IS_PASS_NO,
                'count' => $countEasy + $countMid + $countHard
            ];
        });

        usort($list, function ($a, $b) {
            return $a['count'] < $b['count'];
        });

        return $list;
    }
}
