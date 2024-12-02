<?php

namespace Masterix21\Addressable\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class GeographyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if (!$value) {
            return null;
        }

        $coords = sscanf($value, 'POINT(%f %f)');

        return [
            'lat' => $coords[1],
            'lng' => $coords[0],
        ];
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (! $value) {
            return null;
        }

        $latitude = $value['latitude'] ?? $value['lat'] ?? $value[0] ?? null;
        $longitude = $value['longitude'] ?? $value['lng'] ?? $value[1] ?? null;

        if ($latitude === null || $longitude === null) {
            throw new \InvalidArgumentException('Invalid coordinates format.');
        }

        return sprintf('POINT(%f %f)', $longitude, $latitude);
    }
}
