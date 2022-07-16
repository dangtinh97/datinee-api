<?php

namespace App\Http\Responses;

class ResponseSuccess extends ApiResponse
{
    /**
     * @param array  $response
     * @param string $message
     * @param int    $status
     */
    public function __construct(array $response = [],string $message = 'Thành công!',int $status = 200)
    {
        $this->code = $status;
        $this->message = $message;
        $this->response = $response;
    }
}
