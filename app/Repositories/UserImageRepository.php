<?php

namespace App\Repositories;

use App\Helpers\StrHelper;
use App\Models\UserImage;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class UserImageRepository extends BaseRepository
{
    public function __construct(UserImage $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $images
     *
     * @return bool
     */
    public function deleteImages(array $images):bool
    {
        if(count($images)===0) return false;
        $this->model::query()->raw(function ($collection) use ($images){
            $collection->deleteMany([
                '_id' => [
                    '$in' => array_map(function ($str){
                        return StrHelper::isObjectId($str) ? new ObjectId($str) : "";
                    },$images)
                ],
                'user_id' => Auth::user()->id
            ]);
        });
        return true;
    }

    public function listImage(string $userOid,string $lastOid)
    {
        $pipeline = [
            [
                '$match' => [
                    'user_oid' => new ObjectId($userOid)
                ]
            ],
            [
                '$sort' => [
                    '_id' => -1
                ]
            ],
            [
                '$limit' => 20
            ],
            [
                '$lookup' => [
                    'from' => 'dt_attachments',
                    'localField' => 'attachment_oid',
                    'foreignField' => '_id',
                    'as' => 'images'
                ]
            ],
            [
                '$project' => [
                    'image_oid' => [
                        '$toString' => '$_id'
                    ],
                    'attachment_oid' => [
                        '$toString' => '$attachment_oid'
                    ],

                        'image' => [
                            '$arrayElemAt' => ['$images',0]
                        ]
                ]
            ]
        ];

        if(!empty($lastOid)){
            $pipeline[0]['$match']['_id'] = [
                    '$lt' => new ObjectId($lastOid)
            ];
        }

        return $this->model::raw(function ($collection) use ($pipeline,){
           return $collection->aggregate($pipeline,BaseRepository::OPTION_RESPONSE);
        });
    }
}
