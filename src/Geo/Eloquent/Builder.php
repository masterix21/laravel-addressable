<?php

namespace Masterix21\Addressable\Geo\Eloquent;

use Masterix21\Addressable\Geo\Types\Geometry;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
    public function update(array $values): int
    {
        foreach ($values as &$value) {
            if ($value instanceof Geometry) {
                $value = $this->asWKT($value);
            }
        }

        return parent::update($values);
    }

    protected function asWKT(Geometry $geometry)
    {
        return new GeoExpression($geometry);
    }
}
