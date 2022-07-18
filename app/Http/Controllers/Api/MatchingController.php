<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MatchingService;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    protected MatchingService $matchingService;
    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request)
    {

        $matching = $this->matchingService->index((string)$request->get('last_oid',""));
        return response()->json($matching->toArray());
    }
}
