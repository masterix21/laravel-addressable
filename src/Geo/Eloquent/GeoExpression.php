<?php

namespace Masterix21\Addressable\Geo\Eloquent;

use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Expression;
use Masterix21\Addressable\Geo\Types\Geometry;

/** @property-read Geometry $value */
class GeoExpression extends Expression
{
    public function getValue(Grammar $grammar)
    {
        if ($this->hasSrid()) {
            return "ST_GeomFromText(?, ?)";
        }

        return "ST_GeomFromText(?)";
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
