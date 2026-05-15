<?php

namespace Masterix21\Addressable\Geocoding\Contracts;

use MatanYadaev\EloquentSpatial\Objects\Point;

interface Geocoder
{
    public function geocode(string $address): ?Point;

    /**
     * Resolves a point into textual address fields.
     *
     * @return array<string, string>|null Keys match the Address fillable columns
     *                                    (street_address1, zip, city, state, country).
     */
    public function reverse(Point $point): ?array;
}
