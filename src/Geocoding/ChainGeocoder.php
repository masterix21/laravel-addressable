<?php

namespace Masterix21\Addressable\Geocoding;

use Masterix21\Addressable\Geocoding\Contracts\Geocoder;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Throwable;

class ChainGeocoder implements Geocoder
{
    /** @param array<int, Geocoder> $drivers */
    public function __construct(protected array $drivers)
    {
    }

    public function geocode(string $address): ?Point
    {
        foreach ($this->drivers as $driver) {
            try {
                $point = $driver->geocode($address);
            } catch (Throwable) {
                continue;
            }

            if ($point !== null) {
                return $point;
            }
        }

        return null;
    }

    public function reverse(Point $point): ?array
    {
        foreach ($this->drivers as $driver) {
            try {
                $fields = $driver->reverse($point);
            } catch (Throwable) {
                continue;
            }

            if (! empty($fields)) {
                return $fields;
            }
        }

        return null;
    }
}
