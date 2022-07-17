<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFollowRequest;
use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    protected FollowService $followService;
    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function store(StoreFollowRequest $request)
    {
        $add = $this->followService->store($request->get('user_oid'),$request->get('type'));
        return response()->json($add->toArray());
    }
}
