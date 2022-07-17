<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{

    protected AttachmentService $attachmentService;

    /**
     * @param \App\Services\AttachmentService $attachmentService
     */
    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function store(Request $request)
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');
        $upload = $this->attachmentService->store($file);

        return response()->json($upload->toArray());
    }
}
