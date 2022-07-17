<?php

namespace App\Services;

use App\Helpers\GoogleCloudStorageHelper;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ResponseSuccess;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AttachmentService
{
    protected AttachmentRepository $attachmentRepository;

    /**
     * @param \App\Repositories\AttachmentRepository $attachmentRepository
     */
    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     *
     * @return \App\Http\Responses\ApiResponse
     */
    public function store(UploadedFile $file):ApiResponse
    {
        $name = $file->getClientOriginalName();
        /** @var array $upload */
        $upload = GoogleCloudStorageHelper::upload(file_get_contents($file),Str::slug($name).".{$file->extension()}");
        $create = $this->attachmentRepository->create(array_merge($upload,[
            'mime_type' => $file->getMimeType()
        ]));

        return new ResponseSuccess([
            'attachment_oid' => $create->_id,
            'url' => $upload['full_url'],
            'path' => $upload['path']
        ]);
    }
}
