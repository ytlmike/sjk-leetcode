<?php


namespace App\Http\Controllers;


use App\Models\SjkUser;
use App\Services\Leetcode;
use Illuminate\Http\Request;

class LeetcodeController
{
    /**
     * 手动同步数据
     */
    public function sync(Request $request, Leetcode $leetcode)
    {
        if (!empty($request->input('user_id'))){
            $users = SjkUser::get($request->input('user_id'));
        }else{
            $users = SjkUser::all();
        }
        $leetcode->syncUserSubmit($users);
        //TODO：统一返回
    }

    public function getWeekData(Request $request, Leetcode $leetcode)
    {

    }

    public function getGlobalData()
    {

    }
}
