<?php

namespace App\Http\Controllers;

use App\Traits\OutFormatTrait;
use App\Traits\ValidationTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidationTrait, OutFormatTrait;
}
