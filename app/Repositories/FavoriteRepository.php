<?php

namespace App\Repositories;

use App\Models\Favorite;
use Jenssegers\Mongodb\Eloquent\Model;

class FavoriteRepository extends BaseRepository
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }
}
