<?php

namespace Masterix21\Addressable\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;

trait HasAddresses
{
    use UsesAddressableConfig;

    public function addresses(): MorphMany
    {
        return $this->morphMany($this->addressModel(), 'addressable');
    }
}
