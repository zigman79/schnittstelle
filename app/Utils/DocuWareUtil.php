<?php

namespace App\Utils;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class DocuWareUtil
{
    private $url;
    private $cookie;

    private $client;
    public function __construct($url,$username,$password)
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
                'Accept' => 'application/json'
            ])->post($this->url . '/DocuWare/Platform/Account/Logon', $body);
        $cookies = $response->cookies()->toArray();
        foreach ($cookies as $cookie) {
            if (!empty($cookie['Value'])) {
                $this->cookie .= $cookie['Name'] . "=" . $cookie['Value'] . "; ";
            }
        }
        $this->client = Http::withHeaders([
            'Cookie' => $this->cookie,
            'Accept' => 'application/json'
        ]);
    }

    public function getFiles($fileCabinetId)
    {
        return $this->client->get($this->url . '/DocuWare/Platform/FileCabinets/' . $fileCabinetId . '/Documents')->json();
    }

    public function getFileCabinets()
    {
        return $this->client->get($this->url . '/DocuWare/Platform/FileCabinets')->json();
    }
}



