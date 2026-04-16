<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $url;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->url   = config('services.fonnte.url');
    }

    public function send(string $phone, string $message): bool
    {
        // Clean non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format to 62xxx
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        } elseif (strpos($phone, '8') === 0) {
            $phone = '62' . $phone;
        }
        // If already starts with 62, leave it or handle it. 
        // We'll assume if it starts with 62 it is correct enough for now.

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->url, [
                'target'  => $phone,
                'message' => $message,
            ]);

            Log::info('Fonnte response: ' . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte error: ' . $e->getMessage());
            return false;
        }
    }
}