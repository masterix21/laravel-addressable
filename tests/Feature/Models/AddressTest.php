<?php

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

uses(TestCase::class);

it('return empty addresses collection when eloquent has no addresses', function () {
    $user = User::factory()->createOne();

    expect($user->addresses)->toBeEmpty();
});

it('return addresses collection when eloquent has addresses', function () {
    $user = User::factory()->createOne();

    Address::factory()->addressable($user)->createOne();

    expect($user->addresses)->not->toBeEmpty();
});

it('fires address primary marked event on make primary', function () {
    Event::fake();

    $user = User::factory()->createOne();

    $address = Address::factory()->addressable($user)->createOne();
    $address->markPrimary();

    Event::assertDispatched(AddressPrimaryMarked::class);
});

it('fires address primary unmarked event on make primary', function () {
    Event::fake();

    $user = User::factory()->createOne();

    $address = Address::factory()->addressable($user)->primary()->createOne();

    $address->unmarkPrimary();

    Event::assertDispatched(AddressPrimaryUnmarked::class);
});

it('fires billing address primary marked event on make primary', function () {
    Event::fake();

    $user = User::factory()->createOne();

    Address::factory()->addressable($user)->billing()->createOne();

    $user->billingAddresses->first()->markPrimary();

    Event::assertDispatched(AddressPrimaryMarked::class);
    Event::assertDispatched(BillingAddressPrimaryMarked::class);
});

it('fires billing address primary unmarked event on make primary', function () {
    Event::fake();

    $user = User::factory()->createOne();

    Address::factory()->addressable($user)->primary()->billing()->createOne();

    $user->billingAddress->unmarkPrimary();

    Event::assertDispatched(AddressPrimaryUnmarked::class);
    Event::assertDispatched(BillingAddressPrimaryUnmarked::class);
});

it('fires shipping address primary marked event on make primary', function () {
    Event::fake();

    $user = User::factory()->createOne();

    Address::factory()->addressable($user)->shipping()->createOne();

    $user->shippingAddresses->first()->markPrimary();

    Event::assertDispatched(AddressPrimaryMarked::class);
    Event::assertDispatched(ShippingAddressPrimaryMarked::class);
});

it('fires shipping address primary unmarked event on make primary', function () {
    Event::fake();

    $user = User::factory()->createOne();

    Address::factory()->addressable($user)->primary()->shipping()->createOne();

    $user->shippingAddress->unmarkPrimary();

    Event::assertDispatched(AddressPrimaryUnmarked::class);
    Event::assertDispatched(ShippingAddressPrimaryUnmarked::class);
});

it('stores and retrieves lat/lng correctly', function () {
    $user = User::factory()->createOne();

    $address = Address::factory()->addressable($user)->primary()->shipping()->createOne();

    expect($address->latitude)->not->toBeNull()
        ->and($address->longitude)->not->toBeNull();

    $newLat = fake()->latitude();
    $newLng = fake()->longitude();

    $address
        ->setCoordinates($newLat, $newLng)
        ->save();

    expect($address->latitude)->toBe($newLat)
        ->and($address->longitude)->toBe($newLng);
});
