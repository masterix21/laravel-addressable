<?php

namespace Masterix21\Addressable\Observers;

use Masterix21\Addressable\Jobs\GeocodeAddressJob;
use Masterix21\Addressable\Models\Address;

class AddressObserver
{
    public function saved(Address $address): void
    {
        if (! config('addressable.geocoding.auto', false)) {
            return;
        }

        if (! $this->needsGeocoding($address)) {
            return;
        }

        $job = config('addressable.geocoding.job', GeocodeAddressJob::class);

        $job::dispatch($address);
    }

    protected function needsGeocoding(Address $address): bool
    {
        return $address->coordinates === null && filled($address->display_address);
    }
}
