<?php

namespace App\Repositories;

use App\Helpers\StrHelper;
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
    public function infoMe(string $userOid, bool $getImage=false, bool $getFollow=false, bool $getMatching=false)
    {
        try{
            $pipeline = [
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
            ];

            if($getImage){
                $pipeline[] = [
                    '$lookup' => [
                        'from' => 'dt_user_images',
                        'let' => ['user_id'=>'$id'],
                        'pipeline' => [
                            [
                                '$match' => [
                                    '$expr' => [
                                        '$eq' => ['$user_id','$$user_id']
                                    ]
                                ]
                            ],
                            [
                                '$lookup' => [
                                    'from' => 'dt_attachments',
                                    'localField' => "attachment_oid",
                                    'foreignField' => '_id',
                                    'as' => "attachments"
                                ]
                            ]
                        ],
                        'as' => "images"
                    ]
                ];
            }

            if($getFollow)
            {
                $pipeline[] = [
                    '$lookup' => [
                        'from' => 'dt_follows',
                        'let' => ['user_id'=>'$id'],
                        'pipeline' => [
                            [
                                '$match' => [
                                    '$expr' => [
                                        '$and' =>[
                                            ['$eq'=>['$from_user_id' , Auth::user()->id]],
                                            ['$eq'=>['$follow_user_id', '$$user_id']]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'as' => "follows"
                    ]
                ];
            }

            if($getMatching){
                $pipeline[] = [
                    '$lookup' => [
                        'from' => 'dt_matches',
                        'let' => ['user_id'=>'$id'],
                        'pipeline' => [
                            [
                                '$match' => [
                                    '$expr' => [
                                        '$or' =>[
                                            [
                                                '$and' =>[
                                                    ['$eq'=>['$from_user_id' , Auth::user()->id]],
                                                    ['$eq'=>['$with_user_id', '$$user_id']]
                                                ]
                                            ],
                                            [
                                                '$and' =>[
                                                    ['$eq'=>['$from_user_id' , '$$user_id']],
                                                    ['$eq'=>['$with_user_id', Auth::user()->id]]
                                                ]
                                            ]
                                        ]

                                    ]
                                ]
                            ]
                        ],
                        'as' => "matches"
                    ]
                ];
            }

            return $this->model::raw(function ($collection) use ($userOid, $pipeline) {
                return $collection->aggregate($pipeline,BaseRepository::OPTION_RESPONSE);
            })->first();
        }catch (\Exception $exception){
            dd($exception->getMessage(),$exception->getLine(),$exception->getFile());
        }

    }

    /**
     * @param array  $userIds
     * @param string $lastOid
     *
     * @return mixed
     */
    public function matching(array $userIds, string $lastOid)
    {
        $pipeline = [
            [
                '$match' => [
                    'id' => [
                        '$nin' => $userIds
                    ],
                ]
            ],
            [
                '$sort' => [
                    '_id' => -1
                ]
            ],
            [
                '$limit' => 10
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
                        '$toString' => '$_id'
                    ],
                    'id' => 1,
                    'location' => 1,
                    'age' => 1,
                    'gender' => 1,
                    'full_name' => 1,
                    'introduce' => 1,
                    'avatar' => [
                        '$arrayElemAt' => [
                            '$avatars',0
                        ]
                    ]
                ]
            ]
        ];

        if(StrHelper::isObjectId($lastOid))
        {
            $pipeline[0]['$match']['_id'] = [
                    '$lt' => new ObjectId($lastOid)
            ];
        }


        return $this->model::raw(function ($collection) use ($pipeline){
            return $collection->aggregate($pipeline,BaseRepository::OPTION_RESPONSE);
        });
    }
}
