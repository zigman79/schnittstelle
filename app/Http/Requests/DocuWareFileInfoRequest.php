<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DocuWareFileInfoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination_url' => 'required|string',
            'destination_username' => 'required|string',
            'destination_password' => 'required|string',
            'destination_file_cabinet' => 'required|string',
            'destination_fileid' => 'required|string',
        ];

    }

    protected function failedValidation(Validator $validator)
    {
        if (count($this->request->all()) == 0) {
            throw new HttpResponseException(
                response()->json([], 200)
            );
        }
        $exception = $validator->getException();

        throw (new $exception($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
