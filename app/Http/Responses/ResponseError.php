<?php

namespace App\Http\Responses;

class ResponseError extends ApiResponse
{
    /**
     * @param int    $status
     * @param string $message
     * @param array  $response
     */
    public function __construct(int $status = 500,string $message = 'Hệ thống gián đoạn, vui lòng thử lại sau!',array $response = [])
    {
        $this->code = $status;
        $this->message = $message;
        $this->response = $response;
    }
}
