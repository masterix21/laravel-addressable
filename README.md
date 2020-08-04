# Addresses for any Eloquent model

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masterix21/laravel-addressable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-addressable)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/masterix21/laravel-addressable/run-tests?label=tests)](https://github.com/spatie/laravel-addressable/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/masterix21/laravel-addressable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-addressable)

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

``` php
@TODO
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

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Luca Longo](https://github.com/masterix21)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
