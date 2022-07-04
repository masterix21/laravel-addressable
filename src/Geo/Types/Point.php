<?php

namespace Masterix21\Addressable\Geo\Types;

class Point extends Geometry
{
    public function __construct(
        protected ?float $longitude = null,
        protected ?float $latitude = null,
        protected ?int $srid = null
    ) {
        // ...
    }

    public function setLongitude(?float $value): self
    {
        $this->longitude = $value;

        return $this;
    }

    public function setLatitude(?float $value): self
    {
        $this->latitude = $value;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function toWKT(): string
    {
        return sprintf("POINT(%s)", $this);
    }

    public function toArray(): array
    {
        return [
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'srid' => $this->srid,
        ];
    }

    public function __toString(): string
    {
        return $this->longitude .' '. $this->latitude;
    }
}
