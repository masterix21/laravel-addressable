<?php

namespace Masterix21\Addressable\Geo\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Masterix21\Addressable\Geo\Types\Point;

class PointCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value instanceof Point) {
            return $value;
        }

        if (is_string($value)) {
            return Point::fromWKB($value);
        }

        return $value;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (is_array($value)) {
            $value = new Point(
                longitude: $value['longitude'] ?? $value[0],
                latitude: $value['latitude'] ?? $value[1],
                srid: $value['srid'] ?? $value[2] ?? config('addressable.srid'),
            );
        }

        if (! $value instanceof Point) {
            throw new InvalidArgumentException('The given value is not a Point instance.');
        }

        return $value;
    }
}
