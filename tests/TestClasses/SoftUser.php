<?php

namespace Masterix21\Addressable\Tests\TestClasses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class SoftUser extends Model
{
    use HasAddresses;
    use HasBillingAddresses;
    use HasShippingAddresses;
    use SoftDeletes;

    protected $table = 'soft_users';

    protected $guarded = [];
}
