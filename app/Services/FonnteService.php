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
        if (!$this->token) {
            Log::warning('Fonnte token is not set. Check your environment variables.');
            return false;
        }

        // Clean non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format to 62xxx
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        } elseif (preg_match('/^8/', $phone)) {
            $phone = '62' . $phone;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])
            ->timeout(10)
            ->withOptions(['verify' => false]) // Bypass SSL verification for hosting compatibility
            ->post($this->url, [
                'target'  => $phone,
                'message' => $message,
            ]);

            Log::info('Fonnte response for ' . $phone . ': ' . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Fonnte connection error: ' . $e->getMessage());
            return false;
        }
    }
}