<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Masterix21\Addressable\Events\AddressGeocoded;
use Masterix21\Addressable\Geocoding\Contracts\Geocoder;
use Masterix21\Addressable\Jobs\GeocodeAddressJob;
use Masterix21\Addressable\Tests\TestClasses\QueuedGeocodeAddressJob;
use Masterix21\Addressable\Models\Address;
use Masterix21\Addressable\Tests\TestCase;
use Masterix21\Addressable\Tests\TestClasses\User;
use MatanYadaev\EloquentSpatial\Objects\Point;

uses(TestCase::class);

function nominatimReverseResponse(): array
{
    return [
        'address' => [
            'road' => 'Via Roma',
            'house_number' => '1',
            'postcode' => '20100',
            'city' => 'Milano',
            'state' => 'Lombardia',
            'country_code' => 'it',
        ],
    ];
}

it('geocodes an address via nominatim', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '45.4642', 'lon' => '9.19'],
        ]),
    ]);

    $point = app(Geocoder::class)->geocode('Duomo, Milano');

    expect($point->latitude)->toBe(45.4642)
        ->and($point->longitude)->toBe(9.19);
});

it('falls back to the next driver when the first returns no result', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([]),
        'photon.komoot.io/*' => Http::response([
            'features' => [
                ['geometry' => ['coordinates' => [9.19, 45.4642]]],
            ],
        ]),
    ]);

    $point = app(Geocoder::class)->geocode('Duomo, Milano');

    expect($point->latitude)->toBe(45.4642)
        ->and($point->longitude)->toBe(9.19);
});

it('falls back to the next driver when the first throws', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => fn () => throw new RuntimeException('down'),
        'photon.komoot.io/*' => Http::response([
            'features' => [
                ['geometry' => ['coordinates' => [9.19, 45.4642]]],
            ],
        ]),
    ]);

    expect(app(Geocoder::class)->geocode('Duomo, Milano'))->not->toBeNull();
});

it('returns null when all drivers fail', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([]),
        'photon.komoot.io/*' => Http::response(['features' => []]),
    ]);

    expect(app(Geocoder::class)->geocode('nowhere'))->toBeNull();
});

it('geocodes an address model and fills coordinates', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '45.4642', 'lon' => '9.19'],
        ]),
    ]);

    $user = User::factory()->createOne();

    $address = Address::factory()->addressable($user)->createOne([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
        'country' => 'IT',
    ]);

    expect($address->geocode())->toBeTrue();

    $address->save();

    expect($address->fresh()->coordinates->latitude)->toBe(45.4642)
        ->and($address->fresh()->coordinates->longitude)->toBe(9.19);
});

it('returns false when the address cannot be geocoded', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([]),
        'photon.komoot.io/*' => Http::response(['features' => []]),
    ]);

    $user = User::factory()->createOne();
    $address = Address::factory()->addressable($user)->createOne();

    expect($address->geocode())->toBeFalse();
});

it('reverse geocodes a point into address fields', function () {
    Http::fake([
        'nominatim.openstreetmap.org/reverse*' => Http::response(nominatimReverseResponse()),
    ]);

    $fields = app(Geocoder::class)->reverse(new Point(45.4642, 9.19, 4326));

    expect($fields)->toMatchArray([
        'street_address1' => 'Via Roma 1',
        'zip' => '20100',
        'city' => 'Milano',
        'state' => 'Lombardia',
        'country' => 'IT',
    ]);
});

it('reverse geocodes an address model and fills textual fields', function () {
    Http::fake([
        'nominatim.openstreetmap.org/reverse*' => Http::response(nominatimReverseResponse()),
    ]);

    $user = User::factory()->createOne();
    $address = Address::factory()->addressable($user)
        ->state(['coordinates' => new Point(45.4642, 9.19, 4326)])
        ->createOne();

    expect($address->reverseGeocode())->toBeTrue()
        ->and($address->city)->toBe('Milano')
        ->and($address->country)->toBe('IT');
});

it('returns false on reverse geocode when coordinates are missing', function () {
    $address = new Address;

    expect($address->reverseGeocode())->toBeFalse();
});

it('dispatches the AddressGeocoded event on successful geocode', function () {
    Event::fake([AddressGeocoded::class]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '45.4642', 'lon' => '9.19'],
        ]),
    ]);

    $user = User::factory()->createOne();
    $address = Address::factory()->addressable($user)->createOne([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
    ]);

    $address->geocode();

    Event::assertDispatched(AddressGeocoded::class);
});

it('reverse geocodes via photon when nominatim has no result', function () {
    Http::fake([
        'nominatim.openstreetmap.org/reverse*' => Http::response([]),
        'photon.komoot.io/reverse*' => Http::response([
            'features' => [
                ['properties' => [
                    'street' => 'Via Roma',
                    'housenumber' => '1',
                    'postcode' => '20100',
                    'city' => 'Milano',
                    'state' => 'Lombardia',
                    'countrycode' => 'it',
                ]],
            ],
        ]),
    ]);

    $fields = app(Geocoder::class)->reverse(new Point(45.4642, 9.19, 4326));

    expect($fields)->toMatchArray([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milano',
        'country' => 'IT',
    ]);
});

it('geocodes an address via google', function () {
    config(['addressable.geocoding.drivers' => [
        'google' => [
            'class' => \Masterix21\Addressable\Geocoding\Drivers\GoogleGeocoder::class,
            'endpoint' => 'https://maps.googleapis.com/maps/api/geocode/json',
            'api_key' => 'fake-key',
        ],
    ]]);
    app()->forgetInstance(Geocoder::class);

    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status' => 'OK',
            'results' => [
                ['geometry' => ['location' => ['lat' => 45.4642, 'lng' => 9.19]]],
            ],
        ]),
    ]);

    $point = app(Geocoder::class)->geocode('Duomo, Milano');

    expect($point->latitude)->toBe(45.4642)
        ->and($point->longitude)->toBe(9.19);
});

it('reverse geocodes via google', function () {
    config(['addressable.geocoding.drivers' => [
        'google' => [
            'class' => \Masterix21\Addressable\Geocoding\Drivers\GoogleGeocoder::class,
            'endpoint' => 'https://maps.googleapis.com/maps/api/geocode/json',
            'api_key' => 'fake-key',
        ],
    ]]);
    app()->forgetInstance(Geocoder::class);

    Http::fake([
        'maps.googleapis.com/*' => Http::response([
            'status' => 'OK',
            'results' => [
                ['address_components' => [
                    ['long_name' => 'Via Roma', 'short_name' => 'Via Roma', 'types' => ['route']],
                    ['long_name' => '1', 'short_name' => '1', 'types' => ['street_number']],
                    ['long_name' => '20100', 'short_name' => '20100', 'types' => ['postal_code']],
                    ['long_name' => 'Milano', 'short_name' => 'Milano', 'types' => ['locality']],
                    ['long_name' => 'Lombardia', 'short_name' => 'Lombardia', 'types' => ['administrative_area_level_1']],
                    ['long_name' => 'Italy', 'short_name' => 'IT', 'types' => ['country']],
                ]],
            ],
        ]),
    ]);

    $fields = app(Geocoder::class)->reverse(new Point(45.4642, 9.19, 4326));

    expect($fields)->toMatchArray([
        'street_address1' => 'Via Roma 1',
        'zip' => '20100',
        'city' => 'Milano',
        'country' => 'IT',
    ]);
});

it('returns null from google when the api status is not OK', function () {
    config(['addressable.geocoding.drivers' => [
        'google' => [
            'class' => \Masterix21\Addressable\Geocoding\Drivers\GoogleGeocoder::class,
            'endpoint' => 'https://maps.googleapis.com/maps/api/geocode/json',
            'api_key' => 'fake-key',
        ],
    ]]);
    app()->forgetInstance(Geocoder::class);

    Http::fake([
        'maps.googleapis.com/*' => Http::response(['status' => 'ZERO_RESULTS', 'results' => []]),
    ]);

    expect(app(Geocoder::class)->geocode('nowhere'))->toBeNull();
});

it('does not auto geocode on save when disabled', function () {
    config(['addressable.geocoding.auto' => false]);

    Http::fake();

    $user = User::factory()->createOne();
    Address::factory()->addressable($user)
        ->createOne(['street_address1' => 'Via Roma 1', 'city' => 'Milano']);

    Http::assertNothingSent();
});

it('auto geocodes an address on save when enabled', function () {
    config(['addressable.geocoding.auto' => true]);

    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '45.4642', 'lon' => '9.19'],
        ]),
    ]);

    $user = User::factory()->createOne();
    $address = $user->addAddress([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milan',
        'country' => 'IT',
    ]);

    expect($address->fresh()->coordinates->latitude)->toBe(45.4642);
});

it('dispatches the configured job on save when auto is enabled', function () {
    config(['addressable.geocoding.auto' => true]);

    Bus::fake();

    $user = User::factory()->createOne();
    $user->addAddress(['street_address1' => 'Via Roma 1', 'city' => 'Milan']);

    Bus::assertDispatched(GeocodeAddressJob::class);
});

it('dispatches a queueable job class when configured', function () {
    config([
        'addressable.geocoding.auto' => true,
        'addressable.geocoding.job' => QueuedGeocodeAddressJob::class,
    ]);

    Queue::fake();

    $user = User::factory()->createOne();
    $user->addAddress(['street_address1' => 'Via Roma 1', 'city' => 'Milan']);

    Queue::assertPushed(QueuedGeocodeAddressJob::class);
});

it('does not dispatch the geocoding job when the address already has coordinates', function () {
    config(['addressable.geocoding.auto' => true]);

    Bus::fake();

    $user = User::factory()->createOne();
    Address::factory()->addressable($user)
        ->createOne(['street_address1' => 'Via Roma 1', 'city' => 'Milan']);

    Bus::assertNotDispatched(GeocodeAddressJob::class);
});

it('geocodes and persists the address when the GeocodeAddressJob job runs', function () {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '45.4642', 'lon' => '9.19'],
        ]),
    ]);

    $user = User::factory()->createOne();
    $address = Address::factory()->addressable($user)->createOne([
        'street_address1' => 'Via Roma 1',
        'city' => 'Milan',
    ]);

    (new GeocodeAddressJob($address))->handle();

    expect($address->fresh()->coordinates->latitude)->toBe(45.4642);
});
