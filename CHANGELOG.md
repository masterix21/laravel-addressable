# Changelog

All notable changes to `laravel-addressable` will be documented in this file

## 2.1.1 - 2026-03-11

### Fixed
- Fix `markPrimary()` scoping: no longer affects addresses of other models
- Wrap `markPrimary()` in DB transaction for consistency
- Fix `Address` model to use configured table name via `getTable()` override
- Deduplicate cascade delete when using billing/shipping traits with `HasAddresses`
- Fix typo in `is_shipping` condition
- Fix README typos, incorrect method names, and wrong coordinates

### Added
- Query scopes: `scopePrimary()`, `scopeBilling()`, `scopeShipping()`
- Inverse `addressable()` MorphTo relationship on Address
- Helper methods: `addAddress()`, `primaryAddress()`, `addBillingAddress()`, `addShippingAddress()`
- `meta` JSON column via separate publishable migration
- Configurable `display_format` in config for `displayAddress` accessor

### Changed
- Modernize events with constructor property promotion (PHP 8.2+)
- Remove unused `loadViewsFrom` and `resources/views` directory
- Remove `down()` from migration stub
- Remove obsolete Psalm and php-cs-fixer workflows and configs
- Update CI workflow for PHP 8.2-8.4 and Laravel 11-12

## 1.3.0 - 2024-03-06
- Add support to Laravel 11.x
- Automatically delete addresses when the addressable is deleted

## 1.2.0 - 2023-02-14
- Add support to Laravel 10.x

## 1.1.4 - 2022-10-10
- Changed ST_Distance with ST_Distance_Sphere

## 1.1.3 - 2022-08-17
- Fix wrong lng/lat parameters order

## 1.1.1 - 2022-07-18
- Add point cast to Address model

## 1.1.0 - 2022-07-04
- Add ability to use Point columns
- Query all points between or over another point within X meters

## 1.0.4 - 2021-04-19
- Add support to PHP8

## 1.0.3 - 2020-09-09
- Upgrade to Laravel 8.0

## 1.0.2 - 2020-08-27
- 💅 Add displayAddress attribute
- 🐛 Renamed shipment to shipping

## 1.0.1 - 2020-08-05
- Minor changes and bug fixed

## 1.0.0 - 2020-08-05
- initial release
