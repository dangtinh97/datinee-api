<?php

namespace App\Services;

use App\Helpers\CurlHelper;
use App\Helpers\GoogleCloudStorageHelper;
use App\Helpers\StrHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseError;
use App\Http\Responses\ResponseSuccess;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    /**
     * @param string $email
     * @param string $password
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function registerWithEmail(string $email, string $password):ApiResponse
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOne([
            'email' => $email,
        ]);

        if(!is_null($user) && $user->verify_account) return new ResponseError(201,"Tài khoản email đã được đăng ký");

        if(is_null($user)){
            /** @var User $user */
            $user = $this->create([
                'email' => $email,
                'password' => Hash::make($password),
                'id' => $this->userRepository->counter()
            ]);
        }else{
            $user->update([
                'password' => Hash::make($password)
            ]);
        }

        $data = $this->authLogin($user)->getData();

        $send = Mail::send('mail_datinee', array('url_verify'=>route('verify.email',[
            'token' => StrHelper::securedEncrypt($user->_id,md5($user->_id)),
            'user_id' => $user->id
        ])), function($message) use ($email){
            $message->to($email, 'Visitor')->subject('Datinee xác thực tài khoản!');
        });

        $data['token'] = "";

        return new ResponseSuccess($data,"Chúng tôi đã gửi email xác nhận tài khoản tới địa chỉ của bạn.");

    }

    /**
     * @param int    $userId
     * @param string $encode
     *
     * @return string
     */
    public function verifyEmail(int $userId, string $encode):string
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOne([
            'id' => $userId
        ]);

        if(is_null($user)) return "";

        $userOid = StrHelper::securedDecrypt($encode,md5($user->_id));

        if($userOid==="") return "";
        $user->update([
            'verify_account' => true
        ]);
        $login = $this->authLogin($user);
        return $login->getData()['token'];
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function loginWithEmail(string $email, string $password):ApiResponse
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOne([
            'email' => $email
        ]);
        if(is_null($user) || !$user->verify_account) return $this->registerWithEmail($email,$password);
        if(!Hash::check($password,$user->password)) return new ResponseError(201,'Tên tài khoản hoặc mật khẩu không chính xác');

        return $this->authLogin($user);

    }
}
