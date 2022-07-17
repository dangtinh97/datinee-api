<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInfoMeRequest extends ApiFormRequest
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
            'age' => 'numeric',
            'care_about_gender' => [Rule::in(['MALE', 'FEMALE'])],
            'gender' => [Rule::in(['MALE', 'FEMALE'])],
            'avatar' => 'object_id'
        ];
    }
}
