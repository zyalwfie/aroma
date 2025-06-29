<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;
    protected $origin;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.key');
        $this->baseUrl = config('services.rajaongkir.url', 'https://rajaongkir.komerce.id/api/v1');
        $this->origin = config('services.rajaongkir.origin_city');
    }

    /**
     * Mencari destinasi berdasarkan kata kunci
     *
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchDestination($search, $limit = 10, $offset = 0)
    {
        $cacheKey = 'rajaongkir_search_' . md5($search) . '_' . $limit . '_' . $offset;

        return Cache::remember($cacheKey, 1800, function () use ($search, $limit, $offset) {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->baseUrl . '/destination/domestic-destination', [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['data']) && is_array($data['data'])) {
                    return $data['data'];
                }

                return $data['results'] ?? $data ?? [];
            }

            return [];
        });
    }

    /**
     * Mendapatkan detail destinasi berdasarkan ID
     *
     * @param string $destinationId
     * @return array|null
     */
    public function getDestinationDetail($destinationId)
    {
        $cacheKey = 'rajaongkir_destination_' . $destinationId;

        return Cache::remember($cacheKey, 86400, function () use ($destinationId) {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->baseUrl . '/destination/domestic-destination/' . $destinationId);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data ?? null;
            }

            return null;
        });
    }

    /**
     * Menghitung biaya pengiriman
     *
     * @param string $destination ID destinasi tujuan
     * @param int $weight Berat dalam gram
     * @param string $courier Kode kurir (jne, pos, tiki)
     * @return array
     */
    public function getCost($destination, $weight, $courier)
    {
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->post($this->baseUrl . '/cost', [
            'origin' => $this->origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['rajaongkir']['results'] ?? $data['results'] ?? [];
        }

        return [];
    }

    /**
     * Legacy method - untuk kompatibilitas mundur
     * @deprecated Gunakan searchDestination() sebagai gantinya
     */
    public function getProvinces()
    {
        // Return empty array atau implementasi fallback
        return [];
    }

    /**
     * Legacy method - untuk kompatibilitas mundur
     * @deprecated Gunakan searchDestination() sebagai gantinya
     */
    public function getCities($provinceId = null)
    {
        // Return empty array atau implementasi fallback
        return [];
    }
}
