<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RajaOngkirService;


class RajaOngkirController extends Controller
{
    public function provinces(RajaOngkirService $rajaOngkir)
    {
        return response()->json($rajaOngkir->getProvinces());
    }

    public function cities(Request $request, RajaOngkirService $rajaOngkir)
    {
        $provinceId = $request->query('province_id');
        return response()->json($rajaOngkir->getCities($provinceId));
    }

    public function cost(Request $request, RajaOngkirService $rajaOngkir)
    {
        $validated = $request->validate([
            'destination' => 'required',
            'weight' => 'required|integer',
            'courier' => 'required|string',
        ]);

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

        return response()->json($results);
    }
}
