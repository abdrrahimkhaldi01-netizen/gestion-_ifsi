<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('WHATSAPP_SERVER_URL', 'http://localhost:3000');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/send", [
                'phone'   => $phone,
                'message' => $message,
            ]);
            return $response->successful() && $response->json('success');
        } catch (\Exception $e) {
            Log::error("WhatsApp error: " . $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $contacts): array
    {
        try {
            $response = Http::timeout(60)->post("{$this->baseUrl}/send-bulk", [
                'contacts' => $contacts,
            ]);
            return $response->json('results', []);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function isReady(): bool
    {
        try {
            return Http::timeout(5)->get("{$this->baseUrl}/status")->json('ready', false);
        } catch (\Exception $e) {
            return false;
        }
    }
}