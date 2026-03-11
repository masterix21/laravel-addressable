<?php

namespace Masterix21\Addressable\Events;

use Masterix21\Addressable\Models\Address;

class BillingAddressPrimaryMarked
{
    public function __construct(
        public Address $address,
    ) {}
}
