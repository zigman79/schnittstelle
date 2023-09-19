<?php

namespace App\Http\Controllers;

use ALCales\Docuware\Docuware;
use App\Http\Requests\DocuWareTransferRequest;
use App\Utils\DocuWareUtil;
use Illuminate\Http\Request;

class DocuWareController extends Controller
{
    public function transfer(DocuWareTransferRequest $request)
    {
        $source = new DocuWareUtil($request->get('source_url'),$request->get('source_username'),$request->get('source_password'));
        ray($source->getFileCabinets());
        ray($source->getFiles($request->get('source_file_cabinet')));
    }
}
