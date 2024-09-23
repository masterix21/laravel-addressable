<?php

namespace Masterix21\Addressable\Concerns;

trait UsesAddressableConfig
{
    public function addressModel(): string
    {
        return config('addressable.models.address');
    }

    public function addressesDatabaseTable(): string
    {
        return config('addressable.tables.addresses');
    }
}
