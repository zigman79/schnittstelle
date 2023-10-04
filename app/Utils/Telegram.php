<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class Telegram
{
    public static function sendMessage($message, $chat_id = -4049502534)
    {
        $response = Http::post('https://api.telegram.org/bot'.env('TELEGRAM_KEY').'/sendMessage', [
            'chat_id' => '-4049502534',
            'text' => $message,
            'disable_web_page_preview' => true,
        ]);
        ray($response->body());
    }
}
