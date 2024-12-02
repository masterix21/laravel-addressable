<?php

namespace Masterix21\Addressable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Masterix21\Addressable\Models\Concerns\ImplementsMarkPrimary;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Address extends Model
{
    use HasFactory;
    use HasSpatial;
    use ImplementsMarkPrimary;

    protected $fillable = [
        'addressable_type',
        'addressable_id',
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
    ];

    protected $appends = [
        'display_address',
    ];

    protected $casts = [
        'is_primary' => 'bool',
        'is_billing' => 'bool',
        'is_shipping' => 'bool',
        'coordinates' => Point::class,
    ];



    public function displayAddress(): Attribute
    {
        return Attribute::get(function () {
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
