# Addresses for any Eloquent model

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masterix21/laravel-addressable.svg?style=flat-square)](https://packagist.org/packages/masterix21/laravel-addressable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/masterix21/laravel-addressable/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/masterix21/laravel-addressable/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/masterix21/laravel-addressable.svg?style=flat-square)](https://packagist.org/packages/masterix21/laravel-addressable)


This package adds to any Eloquent model the addresses: in this way will be easier to support a billing address, the shipment addresses or others.

It uses the great package `matanyadaev/laravel-eloquent-spatial` by Matan Yadaev.

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x

## Support us

If you like my work, you can [sponsor me](https://github.com/masterix21).

## Installation

You can install the package via composer:

```bash
composer require masterix21/laravel-addressable
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="config"
```

### Upgrading from 2.1.0

If you're upgrading from a previous version, publish and run the `meta` column migration:

```bash
php artisan vendor:publish --provider="Masterix21\Addressable\AddressableServiceProvider" --tag="addressable-meta-migration"
php artisan migrate
```

## Usage

Extends an Eloquent model to supports the addresses is simple.
``` php
use Masterix21\Addressable\Models\Concerns\HasAddresses;

class User extends Model {
    use HasAddresses;
}

$user->addresses(); // morphMany of `Masterix21\Addressable\Models\Address`
```

`HasAddresses` is a generic trait that implements all addresses code, but if you like to handle the shipping addresses or the billing addresses there are two more specific traits.

```php
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class User extends Model {
    use HasBillingAddresses,
        HasShippingAddresses;
}

$user->billingAddress(); // Primary billing address
$user->billingAddresses(); // All billing addresses

$user->shippingAddress(); // Primary shipping address
$user->shippingAddresses(); // All shipping addresses
```

### Helper methods

Each trait provides convenient helper methods to create addresses:

```php
// Generic address
$address = $user->addAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
    'country' => 'IT',
]);

// Billing address (is_billing is set automatically)
$address = $user->addBillingAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
]);

// Shipping address (is_shipping is set automatically)
$address = $user->addShippingAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
]);

// Get the primary address
$primary = $user->primaryAddress(); // ?Address
```

### Mark and unmark an address as primary

To be sure that only one address per type will be "primary", you can use the `markPrimary()` method. It will mark the address as primary and will unmark the others of the same type, scoped to the same parent model.

```php
$shippingAddress->markPrimary(); // Emits AddressPrimaryMarked and ShippingAddressPrimaryMarked
$shippingAddress->unmarkPrimary(); // Emits AddressPrimaryUnmarked and ShippingAddressPrimaryUnmarked

$billingAddress->markPrimary(); // Emits AddressPrimaryMarked and BillingAddressPrimaryMarked
$billingAddress->unmarkPrimary(); // Emits AddressPrimaryUnmarked and BillingAddressPrimaryUnmarked
```

### Query scopes

The `Address` model provides local scopes for fluent queries:

```php
use Masterix21\Addressable\Models\Address;

Address::query()->primary()->get();  // All primary addresses
Address::query()->billing()->get();  // All billing addresses
Address::query()->shipping()->get(); // All shipping addresses

// Combine scopes
Address::query()->billing()->primary()->first(); // Primary billing address
```

### Inverse relationship

You can access the parent model from an address:

```php
$address->addressable; // Returns the parent model (User, Company, etc.)
```

### Metadata

Each address supports a `meta` JSON column for storing additional data without schema changes:

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

The `display_address` accessor formats the address as a readable string:

```php
$address->display_address; // "Via Roma 1 - 20100 - Milano - IT"
```

You can customize the format in `config/addressable.php`:

```php
'display_format' => '{street_address1}, {street_address2}, {zip} {city}, {state}, {country}',
```

### Create an address with coordinates

```php
$user->addBillingAddress([
    'street_address1' => 'Via Antonio Izzi de Falenta, 7/C',
    'zip' => '88100',
    'city' => 'Catanzaro',
    'state' => 'CZ',
    'country' => 'IT',
    'coordinates' => new Point(38.90852, 16.5894, config('addressable.srid')),
]);
```

### Store latitude and longitude for an address
```php
$billingAddress->coordinates = new Point(38.90852, 16.5894, config('addressable.srid'));
$billingAddress->save();
```

## Query addresses by their distance from/to another point
```php
// Take all addresses within 10 km
$addresses = Address::query()
    ->whereDistanceSphere(
        column: 'coordinates',
        geometryOrColumn: new Point(45.4391, 9.1906, config('addressable.srid')),
        operator: '<=',
        value: 10_000
    )
    ->get();

// Take all addresses over 10 km
$addresses = Address::query()
    ->whereDistanceSphere(
        column: 'coordinates',
        geometryOrColumn: new Point(45.4391, 9.1906, config('addressable.srid')),
        operator: '>=',
        value: 10_000
    )
    ->get();
```

### Add distance as a column

Use `addDistanceTo()` to append the distance from a given point as a column in the result set.
The value is always in **meters**. Divide by `1000` for kilometers, or by `1609.344` for miles.

```php
$origin = new Point(45.4642, 9.1900, config('addressable.srid'));

// Distance in meters (default column name: `distance`)
$addresses = Address::query()
    ->addDistanceTo($origin)
    ->get();

$addresses->first()->distance; // e.g. 1523.4 (meters)

// Custom column name
$addresses = Address::query()
    ->addDistanceTo($origin, as: 'dist_meters')
    ->get();

// Sort by nearest first
$addresses = Address::query()
    ->addDistanceTo($origin)
    ->orderBy('distance')
    ->get();
```

## Testing

``` bash
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
