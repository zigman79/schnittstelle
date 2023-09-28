<?php

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;

class DocuWareUtil
{
    private $url;

    private $cookie;

    private $client;

    private $fileclient;

    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $body = [
            'UserName' => $username,
            'Password' => $password,
            'RememberMe' => false,
            'RedirectToMyselfInCaseOfError' => false,
            'LicenseType' => null,
        ];
        $response = Http::asForm()
            ->withHeaders([
                'Accept' => 'application/json',
            ])->post($this->url.'/DocuWare/Platform/Account/Logon', $body);
        $cookies = $response->cookies()->toArray();
        foreach ($cookies as $cookie) {
            if (! empty($cookie['Value'])) {
                $this->cookie .= $cookie['Name'].'='.$cookie['Value'].'; ';
            }
        }
        $this->client = Http::withHeaders([
            'Cookie' => $this->cookie,
            'Accept' => 'application/json',
        ]);
        $this->fileclient = new Client([
            'headers' => [
                'Cookie' => $this->cookie,
            ],
        ]);

    }

    public function getFiles($fileCabinetId)
    {
        return $this->client->get($this->url.'/DocuWare/Platform/FileCabinets/'.$fileCabinetId.'/Documents')->json();
    }

    public function getFileCabinets()
    {
        return $this->client->get($this->url.'/DocuWare/Platform/FileCabinets')->json();
    }

    public function getFileInfo($fileCabinetId, $documentId)
    {
        return $this->client->get($this->url.'/DocuWare/Platform/FileCabinets/'.$fileCabinetId.'/Documents/'.$documentId)->json();
    }

    public function getFile($fileCabinetId, $documentId): Response
    {
        return $this->fileclient->request('GET', $this->url.'/DocuWare/Platform/FileCabinets/'.$fileCabinetId.'/Documents/'.$documentId.'/FileDownload');
    }

    public function uploadFile($fileCabinetId, $filename, $document)
    {
        return $this->client->attach('file', $document, $filename)->post($this->url.'/DocuWare/Platform/FileCabinets/'.$fileCabinetId.'/Documents');
    }
}
