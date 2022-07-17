<?php

namespace App\Repositories;

use App\Models\SetupApp;
use Jenssegers\Mongodb\Eloquent\Model;

class SetupAppRepository extends BaseRepository
{
    public function __construct(SetupApp $model)
    {
        parent::__construct($model);
    }
}
