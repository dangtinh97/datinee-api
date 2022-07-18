<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

/** @property string $status
 * @property mixed $_id
 */

class ChatRoom extends Model
{
    use HasFactory;
    protected $collection = 'dt_chat_rooms';
    protected $fillable = ['user_id_create','joins','id','type_message','message_last','user_id_send_last','status','deleted_flag'];

    const STATUS_NEW = "NEW";
    const STATUS_CHATTING = "CHATTING";
}
