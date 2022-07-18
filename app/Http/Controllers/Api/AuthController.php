<?php

namespace App\Http\Controllers\Api;

use App\Helpers\StrHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginSocialRequest;
use App\Http\Requests\RegisterWithEmailRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function registerWithEmail(RegisterWithEmailRequest $request):JsonResponse
    {
        $register = $this->authService->registerWithEmail($request->get('email'),$request->get('password'));
        return response()->json($register->toArray());
    }

    /**
     * @param \App\Http\Requests\RegisterWithEmailRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginWithEmail(RegisterWithEmailRequest $request):JsonResponse
    {
        $login = $this->authService->loginWithEmail($request->get('email'),$request->get('password'));
        return response()->json($login->toArray());
    }
}
