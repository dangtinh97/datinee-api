<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $collection = 'dt_favorites';
    protected $fillable = ['user_id','user_oid','key'];

}
