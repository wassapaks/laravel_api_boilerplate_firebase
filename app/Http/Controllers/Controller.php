<?php

namespace App\Http\Controllers;
use App\Classes\ApiResponseClass;
use Illuminate\Support\Facades\Gate;
abstract class Controller
{
    //
    public static function gates($user, $role){
        return Gate::forUser($user)->allows($role);
    }
}
