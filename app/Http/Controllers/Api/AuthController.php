<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginFacebook(Request $request)
    {
        $users = User::query()->get();
        dd($users);
    }
}
