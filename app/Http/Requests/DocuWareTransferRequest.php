<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocuWareTransferRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source_url' => 'required|string',
            'source_username' => 'required|string',
            'source_password' => 'required|string',
            'source_file_cabinet' => 'required|string',
            'source_document_id' => 'required|int',
            'destination_url' => 'required|string',
            'destination_username' => 'required|string',
            'destination_password' => 'required|string',
            'destination_file_cabinet' => 'required|string',
        ];
    }
}
