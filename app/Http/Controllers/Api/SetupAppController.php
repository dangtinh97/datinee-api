<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ResponseSuccess;
use App\Repositories\SetupAppRepository;
use Illuminate\Http\Request;

class SetupAppController extends Controller
{
    protected SetupAppRepository $setupAppRepository;
    public function __construct(SetupAppRepository $setupAppRepository)
    {
        $this->setupAppRepository = $setupAppRepository;
    }

    public function index()
    {
        $setup = $this->setupAppRepository->find([
            'show' => true
        ]);
        return response()->json((new ResponseSuccess([
            'list' => $setup->map(function ($item){
                $data = $item->data;
                if($item->type === "LIST_FAVORITE"){
                    $data = array_map(function ($value){
                        return [
                            'key' => $value['key'],
                            'label' => $value[\request()->header('lang')==="en" ? "en" : "vi"]
                        ];
                    },$data);
                }
                return [
                    'type' => $item->type,
                    'data' => $data
                ];
            })->toArray()
        ]))->toArray());
    }
}
