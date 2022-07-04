<?php

namespace Masterix21\Addressable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Masterix21\Addressable\Geo\Eloquent\ImplementsGeoAttributes;
use Masterix21\Addressable\Models\Concerns\ImplementsMarkPrimary;

class Address extends Model
{
    use HasFactory;
    use ImplementsGeoAttributes;
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
        'position',
    ];

    protected $appends = [
        'display_address',
    ];

    protected $casts = [
        'is_primary' => 'bool',
        'is_billing' => 'bool',
        'is_shipping' => 'bool',
    ];

    public function getDisplayAddressAttribute() : string
    {
        $keys = [
            'street_address1',
            'street_address2',
            'zip',
            'city',
            'state',
            'country',
            'country_code',
        ];

        return collect($this->getAttributes())
            ->filter(fn ($item, $key) => in_array($key, $keys) && ! blank($item))
            ->values()
            ->join(' - ');
    }
}
