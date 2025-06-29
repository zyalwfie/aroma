<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\RajaOngkirService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RajaOngkirController extends Controller
{
    public function searchDestination(Request $request, RajaOngkirService $rajaOngkir)
    {
        $request->validate([
            'search' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0'
        ]);

        $search = $request->input('search');
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        try {
            $destinations = $rajaOngkir->searchDestination($search, $limit, $offset);

            return response()->json([
                'success' => true,
                'data' => $destinations,
                'meta' => [
                    'search' => $search,
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($destinations)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari destinasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDestinationDetail($id, RajaOngkirService $rajaOngkir)
    {
        try {
            $destination = $rajaOngkir->getDestinationDetail($id);

            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destinasi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $destination
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail destinasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cost(Request $request, RajaOngkirService $rajaOngkir)
    {
        $validated = $request->validate([
            'destination' => 'required',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string',
        ]);

        try {
            $couriers = explode(',', $validated['courier']);
            $results = [];

            foreach ($couriers as $courier) {
                $courier = trim($courier);
                $courierResult = $rajaOngkir->getCost(
                    $validated['destination'],
                    $validated['weight'],
                    $courier
                );

                if (!empty($courierResult)) {
                    $results = array_merge($results, $courierResult);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung biaya pengiriman',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function provinces(RajaOngkirService $rajaOngkir)
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint ini sudah tidak didukung. Gunakan /api/destinations/search sebagai gantinya.'
        ], 410);
    }

    public function cities(Request $request, RajaOngkirService $rajaOngkir)
    {
        return response()->json([
            'success' => false,
            'message' => 'Endpoint ini sudah tidak didukung. Gunakan /api/destinations/search sebagai gantinya.'
        ], 410);
    }
}
