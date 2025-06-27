<?php

namespace App\Services;

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
        $this->baseUrl = config('services.rajaongkir.url');
        $this->origin = config('services.rajaongkir.origin_city');
    }

    /**
     * Mendapatkan daftar provinsi
     *
     * @return array
     */
    public function getProvinces()
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($this->baseUrl . '/province');

            if ($response->successful()) {
                return $response->json()['rajaongkir']['results'];
            }

            return [];
        });
    }

    /**
     * Mendapatkan daftar kota berdasarkan ID provinsi (opsional)
     *
     * @param int|null $provinceId
     * @return array
     */
    public function getCities($provinceId = null)
    {
        $cacheKey = 'rajaongkir_cities' . ($provinceId ? '_' . $provinceId : '');

        return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
            $endpoint = $this->baseUrl . '/city';
            $params = [];

            if ($provinceId) {
                $params['province'] = $provinceId;
            }

            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->get($endpoint, $params);

            if ($response->successful()) {
                return $response->json()['rajaongkir']['results'];
            }

            return [];
        });
    }

    /**
     * Menghitung biaya pengiriman
     *
     * @param int $destination ID kota tujuan
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
            return $response->json()['rajaongkir']['results'];
        }

        return [];
    }
}
