<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class Telegram
{

    public static function sendMessage($chat_id, $message)
    {
        $response = Http::post("https://api.telegram.org/bot" . env('TELEGRAM_KEY') . "/sendMessage", [
            "chat_id" => $chat_id,
            "text" => $message,
            "disable_web_page_preview" => true
        ]);
    }

}
