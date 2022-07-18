<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\ChatService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected ChatService $chatService;
    public function __construct(AuthService $authService, ChatService $chatService)
    {
        $this->authService = $authService;
        $this->chatService = $chatService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|View
     */
    public function verifyEmail(Request $request):View
    {
        $userId = (int)$request->get('user_id');
        $encode = $request->get('token');
        $response = $this->authService->verifyEmail($userId,$encode);
        return view('verify_email',compact('response'));
    }

    public function chat(Request $request)
    {
        $userId = (int)$request->get('user_id');
        $withUserId = (int)$request->get('with_user_id');
        $data = [
            [
                'user_id' => 131,
                'user_oid' => '62d375ad33974f41c7051d43',
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmRhdGluZWUudGVzdC9hcGkvbG9naW4td2l0aC1mYWNlYm9vayIsImlhdCI6MTY1ODE2NzI4MSwiZXhwIjoxNjg5NzAzMjgxLCJuYmYiOjE2NTgxNjcyODEsImp0aSI6Ik43NW5PeUttcVlqQUdaZG8iLCJzdWIiOiI2MmQzNzVhZDMzOTc0ZjQxYzcwNTFkNDMiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.jTrwIw5xAVHjNxsGHhIKx2fcKamFYHV5KXrwrkfDPgg'
            ],
            [
                'user_id' => 133,
                'user_oid' => '62d599d8e0899282520a9242',
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmRhdGluZWUudGVzdC9hcGkvbG9naW4td2l0aC1mYWNlYm9vayIsImlhdCI6MTY1ODE2NzMzMywiZXhwIjoxNjg5NzAzMzMzLCJuYmYiOjE2NTgxNjczMzMsImp0aSI6IjZLV0pGMnhNVE5LY0hjVmwiLCJzdWIiOiI2MmQ1OTlkOGUwODk5MjgyNTIwYTkyNDIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.dhd5alixoJ9QUBvxSg2buQToRyXsJrgJvSUoH8YqAcg'
            ],
            [
                'user_id' => 111,
                'user_oid' => '624004ce60ac1090019ab4b1',
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmRhdGluZWUudGVzdC9hcGkvbG9naW4td2l0aC1lbWFpbCIsImlhdCI6MTY1ODE2ODQwOSwiZXhwIjoxNjg5NzA0NDA5LCJuYmYiOjE2NTgxNjg0MDksImp0aSI6IjNWa1pHZzE0WUZXN3diSnciLCJzdWIiOiI2MjQwMDRjZTYwYWMxMDkwMDE5YWI0YjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.5MqN-HXf59J-rFGKd_C67RiLaDb_cycnmVewd3SOERg'
            ]
        ];

        $user = collect($data)->where('user_id','=',$userId)->first();
        $withUser = collect($data)->where('user_id','=',$withUserId)->first();
        $userLogin = Auth::login(User::query()->where('id',$user['user_id'])->first());
        $dataChat = $this->chatService->message($withUser['user_oid'],"")->getData();
        return view('chat',compact('user','dataChat'));
    }
}
