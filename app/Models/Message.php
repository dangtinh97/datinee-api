<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $collection = 'dt_messages';
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'message',
        'type',
        'data',
        'status',
        'reactions'
    ];

    const STATUS_SENT = 'SENT';
    const STATUS_RECEIVED = 'RECEIVED';
    const STATUS_SEEN = 'SEEN';
}
