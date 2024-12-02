<?php

namespace Masterix21\Addressable\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Masterix21\Addressable\Models\Casts\GeographyCast;
use Masterix21\Addressable\Models\Concerns\ImplementsMarkPrimary;

class Address extends Model
{
    use HasFactory;
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
        'coordinates' => GeographyCast::class,
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

    public function latitude(): Attribute
    {
        return Attribute::get(fn () => $this->coordinates['lat'] ?? null);
    }

    public function longitude(): Attribute
    {
        return Attribute::get(fn () => $this->coordinates['lng'] ?? null);
    }

    public function setCoordinates(float $latitude, float $longitude): self
    {
        $this->coordinates = compact('latitude', 'longitude');

        return $this;
    }
}
