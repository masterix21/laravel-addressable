<?php

namespace Masterix21\Addressable\Geo\Types;

class Point extends Geometry
{
    public function __construct(
        protected ?float $latitude = null,
        protected ?float $longitude = null,
        protected ?int $srid = null
    ) {
        // ...
    }

    public function setLatitude(?float $value): self
    {
        $this->latitude = $value;

        return $this;
    }

    public function setLongitude(?float $value): self
    {
        $this->longitude = $value;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function toWKT(): string
    {
        return sprintf("POINT(%s)", $this);
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'srid' => $this->srid,
        ];
    }

    public function __toString(): string
    {
        return $this->latitude .' '. $this->longitude;
    }
}
