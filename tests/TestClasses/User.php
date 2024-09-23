<?php

namespace Masterix21\Addressable\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as BaseUser;
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class User extends BaseUser
{
    use HasAddresses;
    use HasBillingAddresses;
    use HasFactory;
    use HasShippingAddresses;
}
