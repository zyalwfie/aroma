<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
    public function searchDestination(string $search, int $limit = 0, int $offset = 0): array
    {
        $cacheKey = 'rajaongkir_search_' . md5($search) . '_' . $limit . '_' . $offset;

        return Cache::remember($cacheKey, 1800, function () use ($search, $limit, $offset) {
            try {
                Log::info('RajaOngkir API Request', [
                    'url' => $this->baseUrl . '/destination/domestic-destination',
                    'params' => [
                        'search' => $search,
                        'limit' => $limit,
                        'offset' => $offset
                    ]
                ]);

                $response = Http::withHeaders([
                    'key' => $this->apiKey
                ])->get($this->baseUrl . '/destination/domestic-destination', [
                    'search' => $search,
                    'limit' => $limit,
                    'offset' => $offset
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    Log::info('RajaOngkir API Response', [
                        'status' => $response->status(),
                        'has_data' => isset($data['data']),
                        'data_count' => isset($data['data']) ? count($data['data']) : 0
                    ]);

                    if (isset($data['data']) && is_array($data['data'])) {
                        return $data['data'];
                    }

                    return $data['results'] ?? $data ?? [];
                }

                Log::error('RajaOngkir API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('RajaOngkir searchDestination Exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return [];
            }
        });
    }

    public function getDestinationDetail(string $destinationId): ?array
    {
        $cacheKey = 'rajaongkir_destination_' . $destinationId;

        return Cache::remember($cacheKey, 86400, function () use ($destinationId) {
            try {
                $response = Http::withHeaders([
                    'key' => $this->apiKey
                ])->get($this->baseUrl . '/destination/domestic-destination/' . $destinationId);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? $data ?? null;
                }

                Log::error('RajaOngkir getDestinationDetail Error', [
                    'destination_id' => $destinationId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            } catch (\Exception $e) {
                Log::error('RajaOngkir getDestinationDetail Exception', [
                    'destination_id' => $destinationId,
                    'message' => $e->getMessage()
                ]);

                return null;
            }
        });
    }

    public function getCost(string $destination, int $weight, string $courier): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->post($this->baseUrl . "/calculate/domestic-cost?origin=151&destination=$destination&weight=$weight&courier=$courier");

            if ($response->successful()) {
                $data = $response->json();

                Log::info('RajaOngkir getCost data:', ['data' => $data]);

                return $data['data'] ?? [];
            }

            Log::error('RajaOngkir getCost Error', [
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir getCost Exception', [
                'message' => $e->getMessage()
            ]);

            return [];
        }
    }
}
