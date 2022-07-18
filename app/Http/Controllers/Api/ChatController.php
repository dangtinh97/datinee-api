<?php

namespace App\Http\Controllers\Api;

use App\Helpers\StrHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListMessageRequest;
use App\Http\Responses\ResponseError;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected ChatService $chatService;
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function message(ListMessageRequest $request, $userOid)
    {
        if(!StrHelper::isObjectId($userOid)) return response()->json((new ResponseError(422,"user_oid in valid."))->toArray());
        $message = $this->chatService->message($userOid,(string)$request->get('last_oid'));
        return response()->json($message->toArray());
    }
}
