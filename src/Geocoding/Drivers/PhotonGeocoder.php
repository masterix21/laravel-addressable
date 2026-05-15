<?php

namespace Masterix21\Addressable\Geocoding\Drivers;

use Illuminate\Support\Facades\Http;
use Masterix21\Addressable\Geocoding\Contracts\Geocoder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class PhotonGeocoder implements Geocoder
{
    public function __construct(protected array $config)
    {
    }

    public function geocode(string $address): ?Point
    {
        $response = $this->request($this->config['endpoint'], [
            'q' => $address,
            'limit' => 1,
        ]);

        if ($response === null) {
            return null;
        }

        $coordinates = $response->json('features.0.geometry.coordinates');

        if (! is_array($coordinates) || count($coordinates) < 2) {
            return null;
        }

        // GeoJSON stores coordinates as [longitude, latitude].
        return new Point(
            (float) $coordinates[1],
            (float) $coordinates[0],
            $this->config['srid'] ?? 4326,
        );
    }

    public function reverse(Point $point): ?array
    {
        $response = $this->request($this->config['reverse_endpoint'], [
            'lat' => $point->latitude,
            'lon' => $point->longitude,
        ]);

        if ($response === null) {
            return null;
        }

        $properties = $response->json('features.0.properties');

        if (! is_array($properties)) {
            return null;
        }

        $street = trim(($properties['street'] ?? '').' '.($properties['housenumber'] ?? ''));

        return array_filter([
            'street_address1' => $street,
            'zip' => $properties['postcode'] ?? null,
            'city' => $properties['city'] ?? null,
            'state' => $properties['state'] ?? null,
            'country' => isset($properties['countrycode']) ? strtoupper($properties['countrycode']) : null,
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
