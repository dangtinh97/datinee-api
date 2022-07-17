<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $userOid
     *
     * @return mixed
     */
    public function infoMe(string $userOid)
    {
        return $this->model::raw(function ($collection) use ($userOid) {
            return $collection->aggregate([
                [
                    '$match' => [
                        '_id' => (new ObjectId($userOid))
                    ],
                ],
                [
                    '$lookup' => [
                        'from' => 'dt_attachments',
                        'localField' => 'avatar',
                        'foreignField' => '_id',
                        'as' => "avatars"
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'dt_favorites',
                        'localField' => 'id',
                        'foreignField' => 'user_id',
                        'as' => "favorites"
                    ]
                ],
            ],BaseRepository::OPTION_RESPONSE);
        })->first();
    }
}
