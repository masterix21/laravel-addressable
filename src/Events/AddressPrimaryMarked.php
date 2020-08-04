<?php

namespace Masterix21\Addressable\Events;

use Masterix21\Addressable\Models\Address;

class AddressPrimaryMarked
{
    public Address $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }
}
