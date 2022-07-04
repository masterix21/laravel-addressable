<?php

namespace Masterix21\Addressable\Geo\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Masterix21\Addressable\Geo\Types\Geometry;
use Masterix21\Addressable\Models\Address;

/** @mixin Address */
trait ImplementsGeoAttributes
{
    protected function newBaseQueryBuilder(): QueryBuilder
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    protected function performInsert(EloquentBuilder $query, array $options = [])
    {
        $preserveGeometries = [];

        foreach ($this->attributes as $key => $value) {
            if ($value instanceof Geometry) {
                $preserveGeometries[$key] = $value;
                $this->attributes[$key] = new GeoExpression($value);
            }
        }

        $insert = parent::performInsert($query, $options);

        foreach ($preserveGeometries as $key => $value) {
            $this->attributes[$key] = $value;
        }

        return $insert;
    }
}
