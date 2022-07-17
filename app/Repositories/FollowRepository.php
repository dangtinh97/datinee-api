<?php

namespace App\Repositories;

use App\Models\Follow;
use Jenssegers\Mongodb\Eloquent\Model;

class FollowRepository extends BaseRepository
{
    public function __construct(Follow $model)
    {
        parent::__construct($model);
    }
}
