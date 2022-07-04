<?php

namespace Masterix21\Addressable\Geo\Types;

use GeoIO\WKB\Parser\Parser;
use Illuminate\Contracts\Support\Arrayable;
use Stringable;

abstract class Geometry implements Arrayable, Stringable
{
    protected ?int $srid = null;

    abstract public function toWKT(): string;

    public static function fromWKB($wkb): self
    {
        $parser = new Parser(new GeoFactory());

        $srid = unpack('L', substr($wkb, 0, 4))[1];

        $wkb = substr($wkb, 4);

        $parsed = $parser->parse($wkb);

        if ($srid > 0) {
            $parsed->setSrid($srid);
        }

        return $parsed;
    }

    public function getSrid(): int
    {
        return $this->srid ?? config('addressable.srid');
    }

    public function setSrid(int $value): self
    {
        $this->srid = $value;

        return $this;
    }
}
