<?php

namespace Masterix21\Addressable\Models\Concerns;

use Masterix21\Addressable\Concerns\UsesAddressableConfig;

trait HasAddresses
{
    use UsesAddressableConfig;

    public function addresses()
    {
        return $this->morphMany($this->addressModel(), 'addressable');
    }
}
