<?php

namespace App\Services;

use App\Helpers\CurlHelper;
use App\Helpers\GoogleCloudStorageHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseError;
use App\Http\Responses\ResponseSuccess;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Mongodb\Eloquent\Model;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function loginWithFacebook(string $accessToken): ApiResponse
    {
        /** @var array|null $verify */
        $verify = CurlHelper::accessTokenFacebook($accessToken);
        if (is_null($verify)) {
            return new ResponseError(201, "Access token not valid");
        }
        /** @var User|null $user */
        $user = $this->userRepository->findOne([
            'fb_uid' => $verify['fb_uid']
        ]);
        if(is_null($user)){
            $user = $this->create($verify,true);
        }

        return $this->authLogin($user);
    }

    /**
     * @param array $data
     * @param bool  $verifyAccount
     *
     * @return \Jenssegers\Mongodb\Eloquent\Model
     */
    public function create(array $data, bool $verifyAccount = false): Model
    {
        return $this->userRepository->create(array_merge([
            'id' => $this->userRepository->counter(),
            'verify_account' => $verifyAccount,
            'password' => Hash::make('datinee001'),
            'region' => request()->header('lang')==="vi" ? "ASIA" : "USA",
            'status' => User::STATUS_NORMAL
        ],$data));
    }

    /**
     * @param \App\Models\User $user
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function authLogin(User $user): ApiResponse
    {
        $user->update([
            'region' => request()->header('lang') === "vi" ? "ASIA" : "USA"
        ]);
        $token = Auth::guard('api')->login($user);
        return new ResponseSuccess([
            'user_oid' => $user->_id,
            'user_id' => $user->id,
            'full_name' => $user->full_name,
            'age' => $user->age ?? "",
            "avatar" => GoogleCloudStorageHelper::getUrl($user->region ?? "").($user->urlAvatar?->path ?? User::AVATAR_DEFAULT_URL),
            "email" => $user->email ?? "",
            "mobile" => $user->mobile ?? "",
            "register_info_first" => !($user->age===null),
            'token' => $token,
        ]);
    }
}
