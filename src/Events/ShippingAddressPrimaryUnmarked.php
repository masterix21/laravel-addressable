<?php

namespace Masterix21\Addressable\Events;

use Masterix21\Addressable\Models\Address;

class ShippingAddressPrimaryUnmarked
{
    public function __construct(
        public Address $address,
    ) {}
}
