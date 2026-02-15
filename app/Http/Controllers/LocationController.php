<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function reverse(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180']
        ]);

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'EOSHMA ERP Location/1.0'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $validated['latitude'],
                'lon' => $validated['longitude'],
                'addressdetails' => 1
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'data' => null,
                    'message' => 'Reverse geocoding failed.'
                ], 422);
            }

            $body = $response->json();
            $address = $body['address'] ?? [];

            $city = $address['city']
                ?? $address['town']
                ?? $address['municipality']
                ?? $address['village']
                ?? null;

            $province = $address['state']
                ?? $address['county']
                ?? null;

            return response()->json([
                'data' => [
                    'label' => implode(', ', array_filter([$city, $province])) ?: ($body['display_name'] ?? null),
                    'displayName' => $body['display_name'] ?? null,
                    'city' => $city,
                    'province' => $province,
                    'country' => $address['country'] ?? null
                ]
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'data' => null,
                'message' => 'Unable to resolve geolocation.'
            ], 422);
        }
    }
}
