<?php

namespace Masterix21\Addressable\Geocoding\Drivers;

use Illuminate\Support\Facades\Http;
use Masterix21\Addressable\Geocoding\Contracts\Geocoder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class GoogleGeocoder implements Geocoder
{
    public function __construct(protected array $config)
    {
    }

    public function geocode(string $address): ?Point
    {
        $result = $this->request(['address' => $address]);

        $location = $result['geometry']['location'] ?? null;

        if (! is_array($location) || ! isset($location['lat'], $location['lng'])) {
            return null;
        }

        return new Point(
            (float) $location['lat'],
            (float) $location['lng'],
            $this->config['srid'] ?? 4326,
        );
    }

    public function reverse(Point $point): ?array
    {
        $result = $this->request(['latlng' => "{$point->latitude},{$point->longitude}"]);

        $components = $result['address_components'] ?? null;

        if (! is_array($components)) {
            return null;
        }

        $get = function (string $type) use ($components): ?string {
            foreach ($components as $component) {
                if (in_array($type, $component['types'] ?? [], true)) {
                    return $component['short_name'] ?? $component['long_name'] ?? null;
                }
            }

            return null;
        };

        $street = trim(($get('route') ?? '').' '.($get('street_number') ?? ''));

        return array_filter([
            'street_address1' => $street,
            'zip' => $get('postal_code'),
            'city' => $get('locality') ?? $get('administrative_area_level_3'),
            'state' => $get('administrative_area_level_1'),
            'country' => $get('country'),
        ], fn ($value) => filled($value));
    }

    protected function request(array $query): ?array
    {
        $response = Http::get($this->config['endpoint'], array_merge($query, [
            'key' => $this->config['api_key'] ?? null,
        ]));

        if ($response->failed() || $response->json('status') !== 'OK') {
            return null;
        }

        return $response->json('results.0');
    }
}
