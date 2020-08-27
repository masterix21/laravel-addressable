# Addresses for any Eloquent model

[![MIT License](https://img.shields.io/github/license/masterix21/laravel-addressable)](https://img.shields.io/github/license/masterix21/laravel-addressable)
[![Latest Version](https://img.shields.io/github/v/release/masterix21/laravel-addressable)](https://packagist.org/packages/masterix21/laravel-addressable)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/masterix21/laravel-addressable/Tests/master)](https://github.com/masterix21/laravel-addressable/actions?query=workflow%3Arun-tests+branch%3Amaster) 
[![Total Downloads](https://img.shields.io/packagist/dt/masterix21/laravel-addressable.svg)](https://packagist.org/packages/masterix21/laravel-addressable)

This package adds to any Eloquent model the addresses: in this way will be easier to support a billing address, the shipment addresses or others. 

## Support us

If you like my work, you can [sponsoring me](https://github.com/masterix21).

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

## Usage

Extends an Eloquent model to supports the addresses is simple.
``` php
use Masterix21\Addressable\Models\Concerns\HasAddresses;

class User extends Model {
    use HasAddresses;
}

$user->shipments(); // morphMany of `Masterix21\Addressable\Models\Address` 
```

`HasAddress` is a generic trait that will implements all addresses code, but if you like to handle the shipments addresses or the billing address there are other two traits.

```php
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class User extends Model {
    use HasBillingAddresses, 
        HasShippingAddresses;
}

$user->billingAddress(); // Primary billing address
$user->billingAddresses(); // All billing addresses

$user->shipmentAddress(); // Primary shipment address
$user->shipmentAddresses(); // All shipment addresses
```

### Mark and unmark an address as primary
To be sure that only one address per type will be "primary", you can use the `markPrimary()` method. It will mark the address as primary and will unmark the others (of the same type).
```php
$shipmentAddress->markPrimary(); // It will emit the events `AddressPrimaryMarked` and `ShippingAddressPrimaryMarked`
$shipmentAddress->unmarkPrimary(); // It will emit the events `AddressPrimaryUnmarked` and `ShippingAddressPrimaryUnmarked`

$billingAddress->markPrimary(); // It will emit the events `AddressPrimaryMarked` and `BillingAddressPrimaryMarked`
$billingAddress->unmarkPrimary(); // It will emit the events `AddressPrimaryUnmarked` and `BillingAddressPrimaryUnmarked`
```

## Testing

``` bash
composer test
```

## Todo
- [ ] Method to retrieve all nearby addresses of X kilometers

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email l.longo@ambita.it instead of using the issue tracker.

## Credits

- [Luca Longo](https://github.com/masterix21)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
