<?php

namespace App\Utils;

use App\Models\Token;
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
        $hash = md5($this->url);
        $token = Token::whereUrl($hash)->first();
        if ($token == null || $token->expires_at->lessThan(now())) {
            if ($token == null) {
                $token = new Token();
                $token->url = $hash;
            }
            $reponse = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get($this->url.'/DocuWare/Platform/Home/IdentityServiceInfo')->json();
            $serviceurl = $reponse['IdentityServiceUrl'];
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get($serviceurl.'/.well-known/openid-configuration')->json();
            $tokenendpont = $response['token_endpoint'];
            $body = [
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password,
                'scope' => 'docuware.platform',
                'client_id' => 'docuware.platform.net.client',
            ];
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->asForm()->post($tokenendpont, $body)->json();
            $token->token = $response['access_token'];
            $token->expires_at = now()->addSeconds($response['expires_in']);
            $token->save();
        }
        $this->client = Http::withToken($token->token)->withHeaders([
            'Accept' => 'application/json',
        ]);
        $this->fileclient = new Client([
            'headers' => [
                'Authorization' => "Bearer {$token->token}",
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

    public function updateIndexFields($fileCabinetId, $documentId, $data)
    {
        $body = ['Field' => $data];

        return $this->client->put($this->url.'/DocuWare/Platform/FileCabinets/'.$fileCabinetId.'/Documents/'.$documentId.'/Fields', $body);
    }
}
