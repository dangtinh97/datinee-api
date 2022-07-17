<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseSuccess;
use App\Repositories\FollowRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class FollowService
{
    protected FollowRepository $followRepository;
    protected UserRepository $userRepository;
    public function __construct(FollowRepository $followRepository,UserRepository $userRepository)
    {
        $this->followRepository = $followRepository;
        $this->userRepository = $userRepository;
    }


    public function store(string $userOid, string $type):ApiResponse
    {
        $user = $this->userRepository->findOne([
            '_id' => new ObjectId($userOid)
        ]);

        $follow = $this->followRepository->findOne([
           '$or' => [
               [
                   'from_user_id' => Auth::user()->id,
                   'follow_user_id' => $user->id
               ],
               [
                   'from_user_id' => $user->id,
                   'follow_user_id' => Auth::user()->id
               ]
           ]
        ]);

        if(is_null($follow)){
            $this->followRepository->create([
                'from_user_id' => Auth::user()->id,
                'follow_user_id' => $user->id,
                'type' => $type,
                'count' => 1
            ]);
        }else{
            $follow->increment('count',1);
        }

        return new ResponseSuccess();
    }
}
