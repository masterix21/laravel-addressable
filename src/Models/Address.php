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
        'address1',
        'address2',
        'address3',
        'postal_code',
        'city',
        'province',
        'region',
        'country_code',
        'latitude',
        'longitude',
    ];
}
