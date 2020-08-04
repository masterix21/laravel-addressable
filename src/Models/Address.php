<?php

namespace Masterix21\Addressable\Models;

use Illuminate\Database\Eloquent\Model;
use Masterix21\Addressable\Models\Concerns\ImplementsMarkPrimary;

class Address extends Model
{
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
        'country_code',
        'latitude',
        'longitude',
    ];
}
