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
                'attachment_oid' => [
                    '$in' => array_map(function ($str){
                        return StrHelper::isObjectId($str) ? new ObjectId($str) : "";
                    },$images)
                ],
                'user_id' => Auth::user()->id
            ]);
        });
        return true;
    }
}
