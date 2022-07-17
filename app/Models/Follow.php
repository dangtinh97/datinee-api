<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    protected $collection = 'dt_follows';
    protected $fillable = ['from_user_id','follow_user_id','type','count'];
}
