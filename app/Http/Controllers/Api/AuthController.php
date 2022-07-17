<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginSocialRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param \App\Http\Requests\LoginSocialRequest $request
     *
     * @return void
     */
    public function loginFacebook(LoginSocialRequest $request):JsonResponse
    {
        $login = $this->authService->loginWithFacebook($request->get('access_token'));
        return response()->json($login->toArray());
    }
}
