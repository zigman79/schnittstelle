<?php

namespace App\Utils;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Http;

class DocuWareUtil
{
    private $url;
    private $cookie;
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
    }

    public function getFiles($fileCabinetId)
    {
        $response = Http::withHeaders([
            'Cookie' => $this->cookie,
            'Accept' => 'application/json'
        ])->get($this->url . '/DocuWare/Platform/FileCabinets/' . $fileCabinetId . '/Documents');
        return $response->json();
    }

    public function getLoginToken()
    {
        $response = Http::withOptions([
            'cookies' => $this->cookieJar->toArray(),
        ])->get($this->url . '/DocuWare/Platform/Organizations');
        return $response->json();
    }

    public function getFileCabinets()
    {
        $response = Http::withOptions([
            'cookies' => $this->cookieJar,
        ])->get($this->url . '/DocuWare/Platform/FileCabinets');
        ray($response->body());
        return $response->json();
    }
}



