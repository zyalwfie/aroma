<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.rajaongkir.base_url');
        $this->apiKey = config('services.rajaongkir.api_key');
    }

    public function getProvinces()
    {
        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->get("{$this->baseUrl}/province");

        return $response->json()['rajaongkir']['results'] ?? [];
    }

    public function getCities($province_id)
    {
        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->get("{$this->baseUrl}/city", [
            'province' => $province_id,
        ]);

        return $response->json()['rajaongkir']['results'] ?? [];
    }

    public function getCost($destination, $weight = 1000, $courier = 'jne')
    {
        $origin = config('services.rajaongkir.origin');

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->post("{$this->baseUrl}/cost", [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ]);

        return $response->json()['rajaongkir']['results'][0]['costs'] ?? [];
    }
}
