<?php

namespace Masterix21\Addressable\Geo\Eloquent;

use Illuminate\Database\Query\Expression;
use Masterix21\Addressable\Geo\Types\Geometry;

/** @property-read Geometry $value */
class GeoExpression extends Expression
{
    public function getValue()
    {
        if ($this->hasSrid()) {
            return "ST_GeomFromText(?, ?, 'axis-order=long-lat')";
        }

        return "ST_GeomFromText(?, 'axis-order=long-lat')";
    }

    public function getGeoValue(): string
    {
        return $this->value->toWkt();
    }

    public function hasSrid(): bool
    {
        return filled($this->getSrid());
    }

    public function getSrid(): ?int
    {
        return $this->value->getSrid();
    }
}
