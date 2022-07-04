<?php

namespace Masterix21\Addressable\Geo\Eloquent;

use Illuminate\Database\Query\Expression;
use Masterix21\Addressable\Geo\Types\Geometry;

/** @property-read Geometry $value */
class GeoExpression extends Expression
{
    public function getValue()
    {
        return "ST_GeomFromText(?, ?, 'axis-order=long-lat')";
    }

    public function getGeoValue()
    {
        return $this->value->toWkt();
    }

    public function getSrid()
    {
        return $this->value->getSrid();
    }
}
