<?php

namespace App\Services;

use App\Helpers\GoogleCloudStorageHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseSuccess;
use App\Models\User;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class MatchingService
{
    protected UserRepository $userRepository;

    protected FollowRepository $followRepository;
    public function __construct(UserRepository $userRepository,FollowRepository $followRepository)
    {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }

    public function index(string $lastOid):ApiResponse
    {
        $userIds = [Auth::user()->id];

        $list = $this->userRepository->matching($userIds,$lastOid);
        /*"user_oid": "624004ce60ac1090019ab4b1",
                "gender": "",
                "age": 23,
                "address": "",
                "avatar": "https://storage.googleapis.com/datinee-dev/2022328/aaecb2fb-9cc6-4f44-9f05-4ad763bb3110tumblr_bcc875cb54010cef943ffa80604bf127_1e4a1426_500.jpeg",
                "follow": "LIKE",
                "introduce": "",
                "full_name": "Đăng Tính Official",
                "is_matching": false**/


        $data = $list->map(function ($user){
            $avatar = GoogleCloudStorageHelper::getUrl($user->region ?? "") . (!empty($user->avatars) ? $user->avatars[0]['path'] : User::AVATAR_DEFAULT_URL);
            return [
                'user_oid' => $user->_id,
                'user_id' => $user->id,
                'gender' => $user->gender ?? "",
                'age' => $user->age ?? "",
                'latitude' => Arr::get($user->location, 'coordinates.1' , ""),
                'longitude' => Arr::get($user->location, 'coordinates.0' , ""),
                'avatar' => $avatar,
                'introduce' => $user->introduce ?? "",
                'full_name' => $user->full_name ?? ""
            ];
        })->toArray();
        return new ResponseSuccess([
            'users' => $data
        ]);
    }
}
