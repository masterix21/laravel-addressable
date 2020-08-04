<?php

namespace Masterix21\Addressable\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'is_primary',
        'is_billing',
        'is_shipping',
        'address',
        'secondary_address',
        'street_address',
        'street_name',
        'street_suffix',
        'building_number',
        'city',
        'city_prefix',
        'city_suffix',
        'postcode',
        'state',
        'state_abbr',
        'country',
        'country_abbr',
        'latitude',
        'longitude',
    ];

    protected $guarded = [
        'is_primary',
    ];
}
