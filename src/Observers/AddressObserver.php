<?php

namespace Masterix21\Addressable\Observers;

use Masterix21\Addressable\Models\Address;

class AddressObserver
{
    public function saving(Address $address): void
    {
        if (! config('addressable.geocoding.auto', false)) {
            return;
        }

        if ($address->coordinates !== null) {
            return;
        }

        if (blank($address->display_address)) {
            return;
        }

        $address->geocode();
    }
}
