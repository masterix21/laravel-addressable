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
use MatanYadaev\EloquentSpatial\Objects\Point;

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

    expect($address->coordinates->latitude)->not->toBeNull()
        ->and($address->coordinates->longitude)->not->toBeNull();

    $newLat = fake()->latitude();
    $newLng = fake()->longitude();

    $address->coordinates = new Point($newLat, $newLng, config('addressable.srid'));
    $address->save();

    expect($address->coordinates->latitude)->toBe($newLat)
        ->and($address->coordinates->longitude)->toBe($newLng);
});

it('retrieves all address within 10 km', function () {
    $user = User::factory()->createOne();

    $homeAddress = Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4642, 9.19, config('addressable.srid'))])
        ->createOne();

    // Within 10km
    Address::factory()->addressable($user)->state(['coordinates' => new Point(45.4391, 9.1906, config('addressable.srid'))])->createOne();
    Address::factory()->addressable($user)->state(['coordinates' => new Point(45.4535, 9.1898, config('addressable.srid'))])->createOne();
    Address::factory()->addressable($user)->state(['coordinates' => new Point(45.4940, 9.1893, config('addressable.srid'))])->createOne();

    // Within 20km
    Address::factory()->addressable($user)->state(['coordinates' => new Point(45.5436, 9.4197, config('addressable.srid'))])->createOne();
    Address::factory()->addressable($user)->state(['coordinates' => new Point(45.6374, 9.2595, config('addressable.srid'))])->createOne();

    $result = Address::query()
        ->where('id', '!=', $homeAddress->id)
        ->whereDistanceSphere(
            column: 'coordinates',
            geometryOrColumn: new Point(45.4391, 9.1906, config('addressable.srid')),
            operator: '<=',
            value: 10_000
        )
        ->count();

    expect($result)->toBe(3);

    $result = Address::query()
        ->where('id', '!=', $homeAddress->id)
        ->whereDistanceSphere(
            column: 'coordinates',
            geometryOrColumn: new Point(45.4391, 9.1906, config('addressable.srid')),
            operator: '>=',
            value: 10_000
        )
        ->count();

    expect($result)->toBe(2);
});

it('scopes markPrimary to the same addressable model', function () {
    $user1 = User::factory()->createOne();
    $user2 = User::factory()->createOne();

    $state = ['is_billing' => false, 'is_shipping' => false];

    $address1 = Address::factory()->addressable($user1)->primary()->state($state)->createOne();
    $address2 = Address::factory()->addressable($user2)->primary()->state($state)->createOne();

    $newAddress = Address::factory()->addressable($user1)->state($state)->createOne();
    $newAddress->markPrimary();

    expect($address1->fresh()->is_primary)->toBeFalse()
        ->and($address2->fresh()->is_primary)->toBeTrue()
        ->and($newAddress->fresh()->is_primary)->toBeTrue();
});

it('resolves the addressable relationship', function () {
    $user = User::factory()->createOne();

    $address = Address::factory()->addressable($user)->createOne();

    expect($address->addressable)->toBeInstanceOf(User::class)
        ->and($address->addressable->id)->toBe($user->id);
});

it('uses query scopes for primary, billing, and shipping', function () {
    $user = User::factory()->createOne();

    Address::factory()->addressable($user)->state([
        'is_primary' => true,
        'is_billing' => false,
        'is_shipping' => false,
    ])->createOne();

    Address::factory()->addressable($user)->state([
        'is_primary' => false,
        'is_billing' => true,
        'is_shipping' => false,
    ])->createOne();

    Address::factory()->addressable($user)->state([
        'is_primary' => false,
        'is_billing' => false,
        'is_shipping' => true,
    ])->createOne();

    expect(Address::query()->primary()->count())->toBe(1)
        ->and(Address::query()->billing()->count())->toBe(1)
        ->and(Address::query()->shipping()->count())->toBe(1);
});

it('adds an address via helper method', function () {
    $user = User::factory()->createOne();

    $address = $user->addAddress([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
        'country' => 'IT',
    ]);

    expect($address)->toBeInstanceOf(Address::class)
        ->and($address->street_address1)->toBe('Via Roma 1')
        ->and($address->city)->toBe('Milano')
        ->and($user->addresses)->toHaveCount(1);
});

it('adds a billing address via helper method', function () {
    $user = User::factory()->createOne();

    $address = $user->addBillingAddress([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
    ]);

    expect($address->is_billing)->toBeTrue()
        ->and($user->billingAddresses)->toHaveCount(1);
});

it('adds a shipping address via helper method', function () {
    $user = User::factory()->createOne();

    $address = $user->addShippingAddress([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
    ]);

    expect($address->is_shipping)->toBeTrue()
        ->and($user->shippingAddresses)->toHaveCount(1);
});

it('retrieves primary address via helper', function () {
    $user = User::factory()->createOne();

    expect($user->primaryAddress())->toBeNull();

    $address = Address::factory()->addressable($user)->primary()->createOne();

    expect($user->primaryAddress()->id)->toBe($address->id);
});

it('stores and retrieves meta', function () {
    $user = User::factory()->createOne();

    $address = $user->addAddress([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
        'meta' => ['phone' => '+39123456789', 'floor' => 3],
    ]);

    $address->refresh();

    expect($address->meta)->toEqual(['phone' => '+39123456789', 'floor' => 3]);
});

it('adds distance column to query result', function () {
    $user = User::factory()->createOne();

    // Duomo di Milano
    Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4642, 9.1900, config('addressable.srid'))])
        ->createOne();

    // Castello Sforzesco (~1.5 km from the Duomo)
    Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4704, 9.1796, config('addressable.srid'))])
        ->createOne();

    $origin = new Point(45.4642, 9.1900, config('addressable.srid'));

    $addresses = Address::query()
        ->addDistanceTo($origin)
        ->get();

    expect($addresses)->each->toHaveKey('distance');
});

it('returns distance in meters', function () {
    $user = User::factory()->createOne();

    // Duomo di Milano
    Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4642, 9.1900, config('addressable.srid'))])
        ->createOne();

    $origin = new Point(45.4642, 9.1900, config('addressable.srid'));

    $address = Address::query()->addDistanceTo($origin)->first();

    // Same coordinate as origin: distance must be ~0 meters
    expect((float) $address->distance)->toBeLessThan(1);
});

it('returns correct distance between two points in meters', function () {
    $user = User::factory()->createOne();

    // Castello Sforzesco
    Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4704, 9.1796, config('addressable.srid'))])
        ->createOne();

    // Duomo di Milano as origin (~1.5 km from Castello Sforzesco)
    $origin = new Point(45.4642, 9.1900, config('addressable.srid'));

    $address = Address::query()->addDistanceTo($origin)->first();

    // Expected distance ~1500m, with tolerance
    expect((float) $address->distance)->toBeGreaterThan(1000)->toBeLessThan(2000);
});

it('supports custom alias for distance column', function () {
    $user = User::factory()->createOne();

    Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4642, 9.1900, config('addressable.srid'))])
        ->createOne();

    $origin = new Point(45.4642, 9.1900, config('addressable.srid'));

    $address = Address::query()->addDistanceTo($origin, as: 'dist_meters')->first();

    expect($address)->toHaveKey('dist_meters');
});

it('uses configurable display format', function () {
    $user = User::factory()->createOne();

    $address = $user->addAddress([
        'street_address1' => 'Via Roma 1',
        'zip' => '20100',
        'city' => 'Milano',
        'country' => 'IT',
    ]);

    config(['addressable.display_format' => '{street_address1}, {zip} {city}, {country}']);

    expect($address->display_address)->toBe('Via Roma 1, 20100 Milano, IT');
});
