<?php

namespace App\Repositories;

use App\Helpers\StrHelper;
use App\Models\ChatRoom;
use MongoDB\BSON\ObjectId;

class ChatRoomRepository extends BaseRepository
{
    public function __construct(ChatRoom $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int    $userId
     * @param string $lastOid
     *
     * @return mixed
     */
    public function list(int $userId, string $lastOid): mixed
    {
        $pipeline = [
            [
                '$match' => [
                    'joins' => $userId
                ]
            ],
            [
                '$sort' => [
                    'time_send_last' => -1
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'dt_users',
                    'let' => [
                        'joins' => '$joins'
                    ],
                    'pipeline' => [
                        [
                                '$match' => [
                                    '$expr' => [
                                        '$in' => ['$id', '$$joins']
                                    ]
                                ]
                        ],
                        [
                            '$lookup' => [
                                'from' => 'dt_attachments',
                                'localField' => 'avatar',
                                'foreignField' => '_id',
                                'as' => 'avatars'
                            ]
                        ],
                        [
                            '$project' => [
                                'user_oid' => [
                                    '$toString' => '$_id',
                                ],
                                'user_id' => '$id',
                                'full_name' => 1,
                                'avatar' => [
                                    '$arrayElemAt' => [
                                        '$avatars',
                                        0
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'as' => 'users'
                ]
            ]
        ];
        if(StrHelper::isObjectId($lastOid)){
            $pipeline[]['$match']['_id'] = [
                '$lt' => new ObjectId($lastOid)
            ];
        }
        return $this->model::raw(function ($collection) use ($pipeline) {
            return $collection->aggregate($pipeline, BaseRepository::OPTION_RESPONSE);
        });
    }
}
