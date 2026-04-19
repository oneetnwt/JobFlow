<?php

namespace App\Services\PayMongo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    protected string $secretKey;

    protected string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret');
    }

    /**
     * Create a Checkout Session for a plan
     */
    public function createCheckoutSession(array $data)
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->post("{$this->baseUrl}/checkout_sessions", [
                'data' => [
                    'attributes' => $data,
                ],
            ]);

        if ($response->failed()) {
            Log::error('PayMongo Checkout Session Failed', $response->json());
            throw new \Exception('Failed to initialize checkout session.');
        }

        return $response->json();
    }
}
