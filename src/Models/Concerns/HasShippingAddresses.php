<?php

namespace Masterix21\Addressable\Models\Concerns;

use Masterix21\Addressable\Concerns\UsesAddressableConfig;

trait HasShippingAddresses
{
    use UsesAddressableConfig;

    public function shippingAddress()
    {
        return $this->morphOne($this->addressModel(), 'addressable')
            ->where('is_primary', true)
            ->where('is_shipping', true);
    }

    public function shippingAddresses()
    {
        return $this->morphMany($this->addressModel(), 'addressable')
            ->where('is_shipping', true);
    }
}
