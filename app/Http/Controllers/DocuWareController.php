<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocuWareTransferRequest;
use App\Utils\DocuWareUtil;
use App\Utils\Telegram;
use cardinalby\ContentDisposition\ContentDisposition;

class DocuWareController extends Controller
{
    public function transfer(DocuWareTransferRequest $request)
    {

        Telegram::sendMessage('start');
        Telegram::sendMessage($request->fullUrl());
        Telegram::sendMessage($request->post());

        $source = new DocuWareUtil($request->get('source_url'), $request->get('source_username'), $request->get('source_password'));
        /* Get File and Fileinfo */

        $fields = [];
        $fileInfo = $source->getFileInfo($request->get('source_file_cabinet'), $request->get('source_document_id'));
        foreach ($fileInfo['Fields'] as $field) {
            if (isset($field['Item']) && is_string($field['Item'])) {
                $fields[$field['FieldLabel']] = $field['Item'];
            }
        }
        $file = $source->getFile($request->get('source_file_cabinet'), $request->get('source_document_id'));
        if ($file->getStatusCode() != 200) {
            ray($file->getStatusCode());

            return response('Error', $file->getStatusCode());
        }
        $body = $file->getBody()->getContents();
        $filename = ContentDisposition::parse($file->getHeaders()['Content-Disposition'][0]);
        $dest = new DocuWareUtil($request->get('destination_url'), $request->get('destination_username'), $request->get('destination_password'));
        $response = $dest->uploadFile($request->get('destination_file_cabinet'), $filename->getFilename(), $body);
        if ($response->status() != 200) {
            ray($response->status());

            return response('Error', $response->status());
        }
        foreach (json_decode($response->body())->Fields as $field) {
            if ($field->FieldName == 'DWDOCID') {
                return response()->json([
                    'document_id' => $field->Item,
                    'fields' => $fields,
                ]);
            }
        }

        /*  $dest = new DocuWareUtil($request->get('destination_url'), $request->get('destination_username'), $request->get('destination_password'));
          ray($dest->getFileCabinets());
          ray($dest->getFiles($request->get('destination_file_cabinet')));
        */
    }
}
