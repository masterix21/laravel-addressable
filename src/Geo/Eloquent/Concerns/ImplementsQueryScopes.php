<?php

namespace Masterix21\Addressable\Geo\Eloquent\Concerns;

use Illuminate\Support\Facades\DB;
use Masterix21\Addressable\Geo\Eloquent\Builder;
use Masterix21\Addressable\Geo\Types\Point;
use Masterix21\Addressable\Models\Address;

/** @mixin Address */
trait ImplementsQueryScopes
{
    public function scopeWithPositionDistance(Builder $builder, Point $position, ?float $meters = null, string $condition = '<='): Builder
    {
        return $builder
            ->select()
            ->addSelect(DB::raw(sprintf('%s as distance', $this->toStDistanceSql($position))))
            ->whereRaw(sprintf('%s %s %d', $this->toStDistanceSql($position), $condition, $meters));
    }

    protected function toStDistanceSql(Point $position): string
    {
        return sprintf("ST_Distance(position, ST_GeomFromText('%s', %d))", $position->toWKT(), $position->getSrid());
    }
}
