<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramNotifier
{
    public function send(string $botToken, string $chatId, string $message): void
    {
        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}
