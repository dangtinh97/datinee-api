<?php

namespace App\Repositories;

use App\Helpers\StrHelper;
use App\Models\Message;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class MessageRepository extends BaseRepository
{
    public function __construct(Message $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $roomOid
     * @param string $lastOid
     *
     * @return mixed
     */
    public function listMessage(string $roomOid,string $lastOid=""): mixed
    {
//        dd($roomOid);
        $pipeline = [
            [
                '$match' => [
                    'room_oid' => new ObjectId($roomOid)
                ]
            ],
            [
                '$sort' => [
                    '_id' => -1
                ]
            ],
            [
                '$limit' => 30
            ],
            [
                '$lookup' => [
                    'from' => 'dt_attachments',
                    'let' => [
                        'medias' => '$data.medias'
                    ],
                    'pipeline' => [
                        [
                            '$match' => [
                                '$expr' => [
                                    '$in' => ['$_id','$$medias']
                                ]
                            ]

                        ]
                    ],
                    'as' => "medias"
                ]
            ]
        ];


        if(StrHelper::isObjectId($lastOid)){
            $pipeline[0]['$match']['_id'] = [
                '$lt' => new ObjectId($lastOid)
            ];
        }
        return $this->model::raw(function ($collection) use ($pipeline){
            return $collection->aggregate($pipeline, BaseRepository::OPTION_RESPONSE);
        });
    }
}
