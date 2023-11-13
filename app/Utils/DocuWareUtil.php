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
        $this->client = Http::withToken('eyJhbGciOiJSUzI1NiIsImtpZCI6IkVBODgzNTEyODE4OTFEQzU5RUVGQ0ZBQjcxMEQ5REY4NDgxNDdFMjQiLCJ4NXQiOiI2b2cxRW9HSkhjV2U3OC1yY1EyZC1FZ1VmaVEiLCJ0eXAiOiJhdCtqd3QifQ.eyJpc3MiOiJodHRwczovL2xvZ2luLWVtZWEuZG9jdXdhcmUuY2xvdWQvZjQ3NWEwZjUtNjNkNC00ZDdmLTgzNmQtNWZkMGNiNmZlOWI0IiwibmJmIjoxNjk5OTA1NjE2LCJpYXQiOjE2OTk5MDU2MTYsImV4cCI6MTY5OTkwOTIxNiwiYXVkIjoiZG9jdXdhcmUucGxhdGZvcm0iLCJzY29wZSI6WyJkb2N1d2FyZS5wbGF0Zm9ybSJdLCJhbXIiOlsicGFzc3dvcmQiXSwiY2xpZW50X2lkIjoiZG9jdXdhcmUucGxhdGZvcm0ubmV0LmNsaWVudCIsInN1YiI6IjZlODFjZWQ3LTlhOTktNGI5ZS04ODQ2LWM4ZWI3MzA0ZjZlMCIsImF1dGhfdGltZSI6MTY5OTkwNTYxNiwiaWRwIjoibG9jYWwiLCJ1c2VybmFtZSI6IkRXLlNlcnZpY2VzLldTaW50ZXJuIiwidXNlcl9lbWFpbCI6Im1hcmt1cy5iZWNoZXJAYmVjYXJlLnNvbHV0aW9ucyIsIm9yZ2FuaXphdGlvbiI6ImJlLmNhcmUgU29sdXRpb25zIEdtYkggaW50ZXJuIiwib3JnX2d1aWQiOiJmNDc1YTBmNS02M2Q0LTRkN2YtODM2ZC01ZmQwY2I2ZmU5YjQiLCJob3N0X2lkIjoiVW5kZWZpbmVkIiwicHJvZHVjdF90eXBlIjoiUGxhdGZvcm1TZXJ2aWNlIn0.NFGX7HYWzyVWwgXJZ-bRAgXN4BIzI_Ltiwp3Wkbz8nzgMDC51BT4Kmh_WsZF7UcQL7PUlxTZwMgjDMoWMfjVMWDiX0z9WRphrAN_UyqV8neAq3m7EMj2ys_uprKM6xGc7gjQbm9meH0KOVKy3bMhfosWNNovlGcMSlO8PozMffoE9DWXEiVRzp8hvWGN8nq9rokAAIxe4hkpfLxUGx2_Zq9yWQmkV8-f_cj74Kj-7ocaFsQSeh4R-0l9Je5Zj8rJur7yDZqToWA4uXof64jojAwRRCwXEptvYM_pGX83IW5sKav5nW5-DRrKmvdOv6B6_0aMxYLZ1UWP_74F_XSpQw')->withHeaders([
            'Accept' => 'application/json',
        ]);

        return;
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
                ray($cookie);
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
        $body = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => 'docuware.platform',
            'client_id' => 'docuware.platform.net.client',
        ];
        ray($body);
        $res = Http::withHeaders([
            'Accept' => 'application/json',
        ])->asForm()->post('https://login-emea.docuware.cloud/f475a0f5-63d4-4d7f-836d-5fd0cb6fe9b4/connect/token', $body);
        ray($res->body());
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

    public function updateIndexFields($fileCabinetId, $documentId, $data)
    {
        $body = ['Field' => $data];

        return $this->client->put($this->url.'/DocuWare/Platform/FileCabinets/'.$fileCabinetId.'/Documents/'.$documentId.'/Fields', $body);
    }
}
