<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ListMessageRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'last_oid' => 'object_id'
        ];
    }
}
