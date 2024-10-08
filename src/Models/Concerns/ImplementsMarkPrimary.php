<?php

namespace Masterix21\Addressable\Models\Concerns;

use Masterix21\Addressable\Concerns\UsesAddressableConfig;
use Masterix21\Addressable\Events\AddressPrimaryMarked;
use Masterix21\Addressable\Events\AddressPrimaryUnmarked;
use Masterix21\Addressable\Events\BillingAddressPrimaryMarked;
use Masterix21\Addressable\Events\BillingAddressPrimaryUnmarked;
use Masterix21\Addressable\Events\ShippingAddressPrimaryMarked;
use Masterix21\Addressable\Events\ShippingAddressPrimaryUnmarked;

trait ImplementsMarkPrimary
{
    use UsesAddressableConfig;

    public function markPrimary(): void
    {
        $this->is_primary = true;
        $this->save();

        $this->addressModel()::query()
            ->where('is_primary', true)
            ->where('is_billing', $this->is_billing)
            ->where('is_shipping', $this->is_shipment)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        event(new AddressPrimaryMarked($this));

        if ($this->is_billing) {
            event(new BillingAddressPrimaryMarked($this));
        }

        if ($this->is_shipping) {
            event(new ShippingAddressPrimaryMarked($this));
        }
    }

    public function unmarkPrimary(): void
    {
        $this->is_primary = false;
        $this->save();

        event(new AddressPrimaryUnmarked($this));

        if ($this->is_billing) {
            event(new BillingAddressPrimaryUnmarked($this));
        }

        if ($this->is_shipping) {
            event(new ShippingAddressPrimaryUnmarked($this));
        }
    }
}
