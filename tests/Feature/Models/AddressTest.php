<?php

namespace Masterix21\Addressable\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use Masterix21\Addressable\Events\AddressPrimaryMarked;
use Masterix21\Addressable\Events\AddressPrimaryUnmarked;
use Masterix21\Addressable\Events\BillingAddressPrimaryMarked;
use Masterix21\Addressable\Events\BillingAddressPrimaryUnmarked;
use Masterix21\Addressable\Events\ShippingAddressPrimaryMarked;
use Masterix21\Addressable\Events\ShippingAddressPrimaryUnmarked;
use Masterix21\Addressable\Models\Address;
use Masterix21\Addressable\Tests\TestCase;
use Masterix21\Addressable\Tests\TestClasses\User;

class AddressTest extends TestCase
{
    /** @test */
    public function it_return_empty_addresses_collection_when_eloquent_has_no_addresses()
    {
        $user = factory(User::class, 1)->create()->first();

        $this->assertEmpty($user->addresses);
    }

    /** @test */
    public function it_return_addresses_collection_when_eloquent_has_addresses()
    {
        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)->create([ 'addressable_id' => $user->id ]);

        $this->assertNotEmpty($user->addresses);
    }

    /** @test */
    public function it_fires_AddressPrimaryMarked_event_on_make_primary()
    {
        Event::fake();

        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)
            ->create([ 'addressable_id' => $user->id ])
            ->first()
            ->markPrimary();

        Event::assertDispatched(AddressPrimaryMarked::class);
    }

    /** @test */
    public function it_fires_AddressPrimaryUnmarked_event_on_make_primary()
    {
        Event::fake();

        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)
            ->create([
                'addressable_id' => $user->id,
                'is_primary' => true,
            ])
            ->first()
            ->unmarkPrimary();

        Event::assertDispatched(AddressPrimaryUnmarked::class);
    }

    /** @test */
    public function it_fires_BillingAddressPrimaryMarked_event_on_make_primary()
    {
        Event::fake();

        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)
            ->create([
                'addressable_id' => $user->id,
                'is_billing' => true,
            ]);

        $user->billingAddresses->first()->markPrimary();

        Event::assertDispatched(AddressPrimaryMarked::class);
        Event::assertDispatched(BillingAddressPrimaryMarked::class);
    }

    /** @test */
    public function it_fires_BillingAddressPrimaryUnmarked_event_on_make_primary()
    {
        Event::fake();

        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)
            ->create([
                'addressable_id' => $user->id,
                'is_primary' => true,
                'is_billing' => true,
            ]);

        $user->billingAddress->unmarkPrimary();

        Event::assertDispatched(AddressPrimaryUnmarked::class);
        Event::assertDispatched(BillingAddressPrimaryUnmarked::class);
    }

    /** @test */
    public function it_fires_ShippingAddressPrimaryMarked_event_on_make_primary()
    {
        Event::fake();

        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)
            ->create([
                'addressable_id' => $user->id,
                'is_shipping' => true,
            ]);

        $user->shippingAddresses->first()->markPrimary();

        Event::assertDispatched(AddressPrimaryMarked::class);
        Event::assertDispatched(ShippingAddressPrimaryMarked::class);
    }

    /** @test */
    public function it_fires_ShippingAddressPrimaryUnmarked_event_on_make_primary()
    {
        Event::fake();

        $user = factory(User::class, 1)->create()->first();

        factory(Address::class, 1)
            ->create([
                'addressable_id' => $user->id,
                'is_primary' => true,
                'is_shipping' => true,
            ]);

        $user->shippingAddress->unmarkPrimary();

        Event::assertDispatched(AddressPrimaryUnmarked::class);
        Event::assertDispatched(ShippingAddressPrimaryUnmarked::class);
    }
}
