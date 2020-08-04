<?php

namespace Masterix21\Addressable\Events;

use Masterix21\Addressable\Models\Address;

class ShipmentAddressPrimaryUnmarked
{
    public Address $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }
}
