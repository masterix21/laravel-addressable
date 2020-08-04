<?php

namespace Masterix21\Addressable\Tests\TestClasses;

use Illuminate\Foundation\Auth\User as BaseUser;
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class User extends BaseUser
{
    use HasAddresses,
        HasShippingAddresses,
        HasBillingAddresses;
}
