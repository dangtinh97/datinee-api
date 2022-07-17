<?php

namespace App\Http\Middleware;

use App\Http\Responses\ResponseError;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Contracts\Auth\Factory as Auth;
class VerifyJWTToken
{

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard= null)
    {
        if ($this->auth->guard('api')->guest()) {
            return response()->json((new ResponseError(401,"Lỗi xác thực tài khoản"))->toArray());
        }
        return $next($request);
    }
}
