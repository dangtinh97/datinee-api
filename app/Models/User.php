<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Traits\SoftDelete\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model;

/** @property boolean $verify_account */

/** @property string $password */

/** @property int $id
 * @property mixed $_id
 * @property \App\Models\Attachment|null $urlAvatar
 */

class User extends Model implements AuthenticatableContract, AuthorizableContract,JWTSubject
{
    use Authenticatable, SoftDeletes;

    protected $collection = 'dt_users';

    protected $fillable = [
        'id',
        'fb_uid',
        'full_name',
        'age',
        'password',
        'email',
        'mobile',
        'google_id',
        'verify_account',
        'status',
        'region',
        'location',
        'address',
        'care_about_gender',
        'introduce',
        'tokens_notification'
    ];

    const STATUS_NORMAL = "NORMAL";

    const AVATAR_DEFAULT_URL = "/chatbot-default.jpeg";
//    use HasApiTokens, HasFactory, Notifiable;
//
//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array<int, string>
//     */
//    protected $fillable = [
//        'name',
//        'email',
//        'password',
//    ];
//
//    /**
//     * The attributes that should be hidden for serialization.
//     *
//     * @var array<int, string>
//     */
//    protected $hidden = [
//        'password',
//        'remember_token',
//    ];
//
//    /**
//     * The attributes that should be cast.
//     *
//     * @var array<string, string>
//     */
//    protected $casts = [
//        'email_verified_at' => 'datetime',
//    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function can($abilities, $arguments = [])
    {
        // TODO: Implement can() method.
    }

    public function urlAvatar ()
    {
        return $this->hasOne(Attachment::class,'_id','avatar');
    }
}
