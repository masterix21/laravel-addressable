<?php

namespace Masterix21\Addressable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;
use Masterix21\Addressable\Models\Concerns\ImplementsMarkPrimary;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Address extends Model
{
    use HasFactory;
    use HasSpatial;
    use ImplementsMarkPrimary;
    use UsesAddressableConfig;

    protected $fillable = [
        'label',
        'is_primary',
        'is_billing',
        'is_shipping',
        'street_address1',
        'street_address2',
        'zip',
        'city',
        'state',
        'country',
        'coordinates',
        'meta',
    ];

    protected $appends = [
        'display_address',
    ];

    protected $casts = [
        'is_primary' => 'bool',
        'is_billing' => 'bool',
        'is_shipping' => 'bool',
        'coordinates' => Point::class,
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return $this->addressesDatabaseTable();
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function scopeBilling(Builder $query): Builder
    {
        return $query->where('is_billing', true);
    }

    public function scopeShipping(Builder $query): Builder
    {
        return $query->where('is_shipping', true);
    }

    /**
     * Adds a `distance` column (in meters) to the query using ST_DistanceSphere.
     * To convert: divide by 1000 for km, by 1609.344 for miles.
     */
    public function scopeAddDistanceTo(Builder $query, Point $point, string $as = 'distance'): Builder
    {
        return $query->withDistanceSphere('coordinates', $point, $as);
    }

    /**
     * Filters addresses whose coordinates are within $meters from $center.
     */
    public function scopeWithinRadius(Builder $query, Point $center, float $meters): Builder
    {
        return $query->whereDistanceSphere('coordinates', $center, '<=', $meters);
    }

    /**
     * Orders addresses by distance from $origin using ST_DistanceSphere.
     */
    public function scopeOrderByDistance(Builder $query, Point $origin, string $direction = 'asc'): Builder
    {
        return $query->orderByDistanceSphere('coordinates', $origin, $direction);
    }

    /**
     * Fetches the nearest addresses to $origin, with the `distance` column populated.
     * When $limit is null, ordering is applied without limiting the result set.
     */
    public function scopeNearest(Builder $query, Point $origin, ?int $limit = null): Builder
    {
        $query->addDistanceTo($origin)->orderByDistance($origin);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query;
    }

    public function displayAddress(): Attribute
    {
        return Attribute::get(function () {
            $format = config('addressable.display_format');

            if ($format) {
                return preg_replace_callback('/\{(\w+)\}/', function ($matches) {
                    $value = $this->getAttribute($matches[1]);

                    return blank($value) ? '' : $value;
                }, $format);
            }

            $keys = [
                'street_address1',
                'street_address2',
                'zip',
                'city',
                'state',
                'country',
            ];

            return collect($this->getAttributes())
                ->filter(fn ($item, $key) => in_array($key, $keys) && ! blank($item))
                ->values()
                ->join(' - ');
        });
    }
}
