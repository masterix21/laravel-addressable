<?php

namespace Masterix21\Addressable\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Masterix21\Addressable\Models\Address;

/*
 * Geocodes an address and persists it.
 *
 * This base job runs synchronously when dispatched. To run it on a queue,
 * extend it with `implements \Illuminate\Contracts\Queue\ShouldQueue` and
 * point `addressable.geocoding.job` at the subclass.
 */
class GeocodeAddressJob
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Address $address,
    ) {}

    public function handle(): void
    {
        if ($this->address->geocode()) {
            $this->address->save();
        }
    }
}
