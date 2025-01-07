<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LoyaltyService
{
    protected $baseUrl;

    public function __construct()
    {
        // Load the base URL from the configuration
        $this->baseUrl = config('services.loyalty_system.url');
    }

    /**
     * Fetch customers from the Loyalty System API.
     *
     * @return array
     * @throws \Exception
     */
    public function getCustomers()
    {
        try {
            $response = Http::get($this->baseUrl . '/api/customers');
            //$response = Http::get('http://loyalty-system.local/api/customers');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to fetch data. Status code: ' . $response->status());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
