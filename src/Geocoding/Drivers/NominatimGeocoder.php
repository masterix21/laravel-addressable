<?php

namespace Masterix21\Addressable\Geocoding\Drivers;

use Illuminate\Support\Facades\Http;
use Masterix21\Addressable\Geocoding\Contracts\Geocoder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class NominatimGeocoder implements Geocoder
{
    public function __construct(protected array $config)
    {
    }

    public function geocode(string $address): ?Point
    {
        $response = $this->request($this->config['endpoint'], [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);

        if ($response === null) {
            return null;
        }

        $result = $response->json('0');

        if (! is_array($result) || ! isset($result['lat'], $result['lon'])) {
            return null;
        }

        return new Point(
            (float) $result['lat'],
            (float) $result['lon'],
            $this->config['srid'] ?? 4326,
        );
    }

    public function reverse(Point $point): ?array
    {
        $response = $this->request($this->config['reverse_endpoint'], [
            'lat' => $point->latitude,
            'lon' => $point->longitude,
            'format' => 'json',
        ]);

        if ($response === null) {
            return null;
        }

        $address = $response->json('address');

        if (! is_array($address)) {
            return null;
        }

        $street = trim(($address['road'] ?? '').' '.($address['house_number'] ?? ''));

        return array_filter([
            'street_address1' => $street,
            'zip' => $address['postcode'] ?? null,
            'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
            'state' => $address['state'] ?? null,
            'country' => isset($address['country_code']) ? strtoupper($address['country_code']) : null,
        ], fn ($value) => filled($value));
    }

    protected function request(string $url, array $query): mixed
    {
        $response = Http::withHeaders([
            'User-Agent' => $this->config['user_agent'] ?? 'laravel-addressable',
        ])->get($url, $query);

        return $response->failed() ? null : $response;
    }
}
