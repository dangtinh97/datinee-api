<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use MongoDB\BSON\UTCDateTime;

class Controller extends BaseController
{
    public function a(){
        new UTCDateTime();
    }
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
