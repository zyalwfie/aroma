<?php
namespace App\Http\Controllers\Api;

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

        return response()->json(
            $rajaOngkir->getCost($validated['destination'], $validated['weight'], $validated['courier'])
        );
    }
}
