<?php


namespace App\Services;


use App\Models\User;
use App\Traits\CurdTrait;

class UserService
{
    use CurdTrait;

    protected $entityClass = User::class;
}
