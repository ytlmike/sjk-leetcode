<?php


namespace App\Http\Controllers;


use App\Models\SjkUser;

class UserController
{
    public function getUsers()
    {
        return SjkUser::all();
    }
}
