<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiFormRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        if($validator->errors()->count()>0)
            throw new HttpResponseException(response()->json(( new ResponseError(422,$validator->errors()->first()))->toArray()));
    }
}
