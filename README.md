# Make your Eloquent models addressable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masterix21/laravel-addressable.svg?style=flat-square)](https://packagist.org/packages/masterix21/laravel-addressable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/masterix21/laravel-addressable/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/masterix21/laravel-addressable/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/masterix21/laravel-addressable.svg?style=flat-square)](https://packagist.org/packages/masterix21/laravel-addressable)

Attach any number of addresses to any Eloquent model through a polymorphic relation. Built on top of [`matanyadaev/laravel-eloquent-spatial`](https://github.com/MatanYadaev/laravel-eloquent-spatial), it natively supports geospatial coordinates and distance queries — perfect for billing, shipping or any location-aware use case.

## Features

- Polymorphic `addresses()` relation for any Eloquent model
- Dedicated `billing` and `shipping` traits with primary-address shortcuts
- Eager-loadable `primaryAddress`, `billingAddress`, `shippingAddress` relations
- Primary-address toggling, scoped per address type, with events
- Geospatial `POINT` column with distance queries, `withinRadius` scope and optional spatial index
- Free-form `meta` JSON column for extra data
- Configurable `display_address` accessor
- SoftDeletes-aware cascade delete of addresses when the parent model is deleted
- Pluggable `Address` model and table name

## Requirements

- PHP 8.2+
- Laravel 11.x, 12.x or 13.x (Laravel 13 requires PHP 8.3+)
- A database with spatial support (MySQL 8+, MariaDB 10.5+, PostgreSQL with PostGIS)

## Support us

If you like my work, you can [sponsor me](https://github.com/sponsors/masterix21).

## Installation

Install the package via Composer:

```bash
composer require masterix21/laravel-addressable
```

Publish and run the migrations:

```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="migrations"
php artisan migrate
```

Optionally publish the config file:

```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="config"
```

### Upgrading from 2.1.x

Publish and run the additional `meta` column migration:

```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="addressable-meta-migration"
php artisan migrate
```

### Upgrading from 2.2.x

**Spatial index (optional but recommended).** Publish and run the spatial index migration to make `coordinates` indexed for fast distance queries:

```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="addressable-spatial-index-migration"
php artisan migrate
```

The migration:
- Backfills any row with `NULL` coordinates to `POINT(0, 0)` with the configured SRID.
- Alters `coordinates` to `NOT NULL` with a `POINT(0, 0)` default, so addresses created without explicit coordinates keep working.
- Adds a `SPATIAL INDEX` on `coordinates`.

**`primaryAddress` is now a relation.** It used to be a method returning `?Address`. It now returns a `MorphOne` relation, which means:

- `$user->primaryAddress` still returns `?Address` (unchanged via property access).
- `$user->primaryAddress()` now returns a relation builder, **not** the model. If you were calling it as a method, switch to the property or append `->first()`.
- You can now eager load it: `User::with('primaryAddress')->get()`.

## Configuration

The published `config/addressable.php` file exposes:

```php
return [
    'models' => [
        // Swap with your own model (e.g. to use UUIDs).
        'address' => \Masterix21\Addressable\Models\Address::class,
    ],

    'tables' => [
        // Change before running the migration.
        'addresses' => 'addresses',
    ],

    // SRID used for the POINT column. 4326 = WGS84 (lat/lng).
    'srid' => 4326,

    // Template for the display_address accessor. Use {field_name} placeholders.
    // Set to null to fall back to the default " - " separated format.
    'display_format' => null,
];
```

## Usage

### Attach addresses to a model

```php
use Masterix21\Addressable\Models\Concerns\HasAddresses;

class User extends Model
{
    use HasAddresses;
}

$user->addresses; // MorphMany of Masterix21\Addressable\Models\Address
```

`HasAddresses` is the generic trait. For billing or shipping flows, use the dedicated traits (they can be combined):

```php
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class User extends Model
{
    use HasBillingAddresses, HasShippingAddresses;
}

$user->billingAddress;    // Primary billing address (MorphOne)
$user->billingAddresses;  // All billing addresses (MorphMany)

$user->shippingAddress;   // Primary shipping address (MorphOne)
$user->shippingAddresses; // All shipping addresses (MorphMany)
```

When the parent model is hard-deleted, its addresses are automatically removed. If the parent uses `SoftDeletes`, addresses survive soft-delete and are removed only on `forceDelete()`.

### Create addresses

```php
// Generic address
$user->addAddress([
    'label' => 'Home',
    'street_address1' => 'Via Roma 1',
    'zip' => '20100',
    'city' => 'Milano',
    'state' => 'MI',
    'country' => 'IT',
]);

// Billing address — is_billing is set automatically
$user->addBillingAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
]);

// Shipping address — is_shipping is set automatically
$user->addShippingAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
]);

// Fetch the primary address (any type) via the eager-loadable relation
$user->primaryAddress;                  // ?Address
User::with('primaryAddress')->get();    // eager loaded
```

### Address fields

| Field             | Type     | Notes                                |
|-------------------|----------|--------------------------------------|
| `label`           | string   | Optional tag (e.g. "Home", "Office") |
| `is_primary`      | bool     | Toggled via `markPrimary()`          |
| `is_billing`      | bool     | Set automatically by the helper      |
| `is_shipping`     | bool     | Set automatically by the helper      |
| `street_address1` | string   |                                      |
| `street_address2` | string   |                                      |
| `zip`             | string   |                                      |
| `city`            | string   |                                      |
| `state`           | string   |                                      |
| `country`         | string   | ISO alpha-2/3 (max 4 chars)          |
| `coordinates`     | `Point`  | Cast to a spatial Point object       |
| `meta`            | array    | JSON column for arbitrary data       |

### Mark an address as primary

`markPrimary()` ensures a single primary address per type, scoped to the same parent model. It is wrapped in a transaction and unmarks any other primary address of the same kind.

```php
$shippingAddress->markPrimary();
$shippingAddress->unmarkPrimary();

$billingAddress->markPrimary();
$billingAddress->unmarkPrimary();
```

### Events

Every primary toggle dispatches dedicated events (each carrying the `Address` instance):

| Action            | Generic event            | Billing event                   | Shipping event                  |
|-------------------|--------------------------|---------------------------------|---------------------------------|
| `markPrimary()`   | `AddressPrimaryMarked`   | `BillingAddressPrimaryMarked`   | `ShippingAddressPrimaryMarked`  |
| `unmarkPrimary()` | `AddressPrimaryUnmarked` | `BillingAddressPrimaryUnmarked` | `ShippingAddressPrimaryUnmarked`|

All events live in `Masterix21\Addressable\Events`. Billing/shipping variants fire only when the respective flag is set on the address.

### Query scopes

```php
use Masterix21\Addressable\Models\Address;

Address::query()->primary()->get();
Address::query()->billing()->get();
Address::query()->shipping()->get();

// Scopes are composable
Address::query()->billing()->primary()->first();
```

### Inverse relationship

```php
$address->addressable; // The parent model (User, Company, ...)
```

### Metadata

Every address has a JSON `meta` column for extra data without touching the schema:

```php
$user->addAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
    'meta' => [
        'phone' => '+39 02 1234567',
        'floor' => 3,
        'notes' => 'Ring twice',
    ],
]);

$address->meta['phone']; // '+39 02 1234567'
```

### Display address

The `display_address` accessor returns a readable representation:

```php
$address->display_address; // "Via Roma 1 - 20100 - Milano - MI - IT"
```

Customize the format in `config/addressable.php`:

```php
'display_format' => '{street_address1}, {street_address2}, {zip} {city}, {state}, {country}',
```

## Geospatial features

### Store coordinates

```php
use MatanYadaev\EloquentSpatial\Objects\Point;

$user->addBillingAddress([
    'street_address1' => 'Via Antonio Izzi de Falenta, 7/C',
    'zip' => '88100',
    'city' => 'Catanzaro',
    'state' => 'CZ',
    'country' => 'IT',
    'coordinates' => new Point(38.90852, 16.5894, config('addressable.srid')),
]);

// Or assign later
$billingAddress->coordinates = new Point(38.90852, 16.5894, config('addressable.srid'));
$billingAddress->save();
```

### Filter by distance

Use the `withinRadius` scope for the common case of "addresses within N meters of a point":

```php
use MatanYadaev\EloquentSpatial\Objects\Point;

$milano = new Point(45.4391, 9.1906, config('addressable.srid'));

// Addresses within 10 km of Milano
Address::query()->withinRadius($milano, 10_000)->get();
```

For custom comparisons (`<`, `>=`, etc.) drop down to the underlying spatial scope:

```php
Address::query()
    ->whereDistanceSphere(
        column: 'coordinates',
        geometryOrColumn: $milano,
        operator: '>=',
        value: 10_000,
    )
    ->get();
```

### Add distance as a column

`addDistanceTo()` appends the distance from a given point (always in **meters**) as an extra column. Divide by `1000` for kilometers, by `1609.344` for miles.

```php
$origin = new Point(45.4642, 9.1900, config('addressable.srid'));

// Default column name: `distance`
$addresses = Address::query()
    ->addDistanceTo($origin)
    ->get();

$addresses->first()->distance; // e.g. 1523.4

// Custom column name
Address::query()->addDistanceTo($origin, as: 'dist_meters')->get();

// Nearest first
Address::query()->addDistanceTo($origin)->orderBy('distance')->get();
```

### Order by distance and find nearest

`orderByDistance()` sorts addresses by distance from a point without adding any column. `nearest()` is the high-level helper for "give me the N closest addresses": it adds the `distance` column, orders ascending and optionally applies a limit.

```php
$milano = new Point(45.4642, 9.1900, config('addressable.srid'));

// The 5 addresses closest to Milano, each with a populated `distance` (meters)
$closest = Address::query()->nearest($milano, 5)->get();

$closest->first()->distance; // e.g. 42.1

// Composable with any other scope
Address::query()->billing()->nearest($milano, 3)->get();

// Without a limit, ordering is applied but the result set is not truncated
Address::query()->shipping()->nearest($milano)->paginate(20);

// Ordering only, no `distance` column
Address::query()->orderByDistance($milano, 'desc')->get();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email l.longo@ambita.it instead of using the issue tracker.

## Credits

- [Luca Longo](https://github.com/masterix21)
- [Matan Yadaev](https://github.com/MatanYadaev/laravel-eloquent-spatial)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
