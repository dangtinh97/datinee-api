<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UserImage extends Model
{
    use HasFactory;
    protected $collection = 'dt_user_images';
    protected $fillable = ['attachment_oid','user_id','user_oid','type','status'];

    const STATUS_NORMAL = "NORMAL";
    const TYPE_PHOTO = "PHOTO";
}
