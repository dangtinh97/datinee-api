<?php

namespace App\Repositories;

use App\Models\ChatRoom;
use Jenssegers\Mongodb\Eloquent\Model;

class ChatRoomRepository extends BaseRepository
{
    public function __construct(ChatRoom $model)
    {
        parent::__construct($model);
    }
}
