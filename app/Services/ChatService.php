<?php

namespace App\Services;

use App\Helpers\GoogleCloudStorageHelper;
use App\Helpers\StrHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseSuccess;
use App\Models\ChatRoom;
use App\Models\User;
use App\Repositories\ChatRoomRepository;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class ChatService
{
    protected UserRepository $userRepository;

    protected ChatRoomRepository $chatRoomRepository;

    protected MessageRepository $messageRepository;

    public function __construct(UserRepository $userRepository, ChatRoomRepository $chatRoomRepository, MessageRepository $messageRepository)
    {
        $this->userRepository = $userRepository;
        $this->chatRoomRepository = $chatRoomRepository;
        $this->messageRepository = $messageRepository;
    }

    public function message(string $userOid, string $lastOid): ApiResponse
    {
        /** @var \App\Models\User $withUser */
        $withUser = $this->userRepository->findOne([
            '_id' => new ObjectId($userOid)
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @var \App\Models\ChatRoom|null $room */
        $room = $this->chatRoomRepository->findOne([
            '$or' => [
                ['joins' => [$user->id, $withUser->id]],
                ['joins' => [$withUser->id, $user->id]],
            ],
            'deleted_flag' => false
        ]);
        $create = false;
        if(is_null($room)){
            $create = true;
            /** @var \App\Models\ChatRoom $room */
            $room = $this->chatRoomRepository->create([
                'user_id_create' => $user->id,
                'joins' => [$withUser->id, $user->id],
                'status' => ChatRoom::STATUS_NEW,
                'deleted_at' => false
            ]);
        }
        $messages = [];
        if(!$create && $room->status===ChatRoom::STATUS_CHATTING)
        {
            $messages = $this->messageRepository->listMessage($room->_id,$lastOid)
                ->map(function ($message){
                    return [
                        'message_oid'=>$message->_id,
                        'from_user_id'=>$message->from_user_id,
                        'to_user_id' => $message->to_user_id,
                        'message' => $message->message ?? "",
                        'type' => $message->type ?? "",
                        'data' => array_merge($message->data,[
                            'medias' => array_map(function ($item){
                                return [
                                    'type' => strtoupper(explode('/',$item['mime_type'])[0]),
                                    'url' => GoogleCloudStorageHelper::getUrl().$item['path'],
                                    'attachment_oid' => $item['_id']->__toString()
                                ];
                            },$message->medias ?? [])
                        ]),
                        'is_group' => $message->is_group ?? false,
                        'time_send' => '1 phút trước - '.$message->created_at->__toString()
                    ];
                })->toArray();
        }

        $dataWithUser = [
            'user_oid' => $withUser->_id,
            'user_id' => $withUser->id,
            'full_name' => $withUser->full_name ?? "",
            'avatar' => StrHelper::urlFromAttachment($withUser->urlAvatar)
        ];

        return new ResponseSuccess([
            'to_user' => $dataWithUser,
            'room_oid' => $room->_id,
            'messages' => array_reverse($messages)
        ]);
    }

    /**
     * @param string $lastOid
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function room(string $lastOid):ApiResponse
    {
        $list = $this->chatRoomRepository->list(Auth::user()->id,$lastOid)->map(function ($item){
            $user = collect($item->users)->where('user_id','!=',Auth::user()->id)->map(function ($item){
                return [
                    'user_oid' => $item['user_oid'],
                    'user_id' => $item['user_id'],
                    'full_name' => $item['full_name'] ?? "",
                    'avatar' => !empty($item['avatar']) ? GoogleCloudStorageHelper::getUrl().$item['avatar']['path'] : GoogleCloudStorageHelper::getUrl().User::AVATAR_DEFAULT_URL
                ];
            })->first();
            return [
                'room_oid' => $item->_id,
                'message' => $item->message_last ?? "",
                'type' => $item->type_message ?? "",
                'from_user_id' => $item->user_id_send_last ?? "",
                'time_send' => !is_null($item->time_send_last) ? date('d/m/Y H:i',(int)$item->time_send_last->__toString()/1000) : "",
                'user' =>$user
            ];
        })->toArray();
        return new ResponseSuccess([
            'list' => $list
        ]);
    }
}
