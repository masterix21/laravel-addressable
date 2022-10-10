<?php

namespace Masterix21\Addressable\Geo\Eloquent\Concerns;

use Illuminate\Support\Facades\DB;
use Masterix21\Addressable\Geo\Eloquent\Builder;
use Masterix21\Addressable\Geo\Types\Point;
use Masterix21\Addressable\Models\Address;

/** @mixin Address */
trait ImplementsQueryScopes
{
    /**
     * @param Builder           $builder
     * @param Point      $position
     * @param float|null $meters
     * @param string     $condition
     *
     * @return Builder
     */
    public function scopeWithPositionDistance($builder, Point $position, ?float $meters = null, string $condition = '<=')
    {
        return $builder
            ->select()
            ->addSelect(DB::raw(sprintf('%s as distance', $this->toStDistanceSql($position))))
            ->whereRaw(sprintf('%s %s %d', $this->toStDistanceSql($position), $condition, $meters));
    }

    protected function toStDistanceSql(Point $position): string
    {
        return sprintf("ST_Distance_Sphere(position, ST_GeomFromText('%s', %d))", $position->toWKT(), $position->getSrid());
    }
}
