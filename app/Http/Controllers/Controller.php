<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use app\Models\User;

abstract class Controller
{
    protected function user(): User
    {
        /** @var User $user */
        $user = Auth::user();
        
        return $user;
    }
}
