<?php

namespace Masterix21\Addressable\Models\Concerns;

use Masterix21\Addressable\Concerns\UsesAddressableConfig;

trait HasBillingAddresses
{
    use UsesAddressableConfig;

    public function billingAddress()
    {
        return $this->morphOne($this->addressModel(), 'addressable')
            ->where('is_primary', true)
            ->where('is_billing', true);
    }

    public function billingAddresses()
    {
        return $this->morphMany($this->addressModel(), 'addressable')
            ->where('is_billing', true);
    }
}
