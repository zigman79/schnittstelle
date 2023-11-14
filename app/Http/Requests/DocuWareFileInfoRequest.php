<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
