<?php

namespace Masterix21\Addressable\Geo\Types;

use GeoIO\Factory;

class GeoFactory implements Factory
{
    public function createPoint($dimension, array $coordinates, $srid = null): Point
    {
        return new Point($coordinates['x'], $coordinates['y'], $srid);
    }

    public function createLineString($dimension, array $points, $srid = null)
    {
        // TODO: Implement createLineString() method.
    }

    public function createLinearRing($dimension, array $points, $srid = null)
    {
        // TODO: Implement createLinearRing() method.
    }

    public function createPolygon($dimension, array $lineStrings, $srid = null)
    {
        // TODO: Implement createPolygon() method.
    }

    public function createMultiPoint($dimension, array $points, $srid = null)
    {
        // TODO: Implement createMultiPoint() method.
    }

    public function createMultiLineString($dimension, array $lineStrings, $srid = null)
    {
        // TODO: Implement createMultiLineString() method.
    }

    public function createMultiPolygon($dimension, array $polygons, $srid = null)
    {
        // TODO: Implement createMultiPolygon() method.
    }

    public function createGeometryCollection($dimension, array $geometries, $srid = null)
    {
        // TODO: Implement createGeometryCollection() method.
    }
}
