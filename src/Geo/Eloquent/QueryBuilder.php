<?php

namespace Masterix21\Addressable\Geo\Eloquent;

class QueryBuilder extends \Illuminate\Database\Query\Builder
{
    public function cleanBindings(array $bindings): array
    {
        $geoBindings = [];

        foreach ($bindings as &$binding) {
            if (! $binding instanceof GeoExpression) {
                $geoBindings[] = $binding;
                continue;
            }

            $geoBindings[] = $binding->getGeoValue();
            $geoBindings[] = $binding->getSrid();
        }

        return parent::cleanBindings($geoBindings);
    }
}
