<?php

namespace Masterix21\Addressable\Tests\TestClasses;

use Illuminate\Contracts\Queue\ShouldQueue;
use Masterix21\Addressable\Jobs\GeocodeAddressJob;

class QueuedGeocodeAddressJob extends GeocodeAddressJob implements ShouldQueue
{
}
