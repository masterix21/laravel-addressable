# Laravel Addressable

`masterix21/laravel-addressable` attaches addresses to any Eloquent model via a
polymorphic relation. Addresses support primary/billing/shipping flags, spatial
coordinates, JSON metadata, and geocoding.

## Making a model addressable

Add one of the traits to the model. Use the specific traits only when billing or
shipping addresses are needed.

```php
use Masterix21\Addressable\Models\Concerns\HasAddresses;
use Masterix21\Addressable\Models\Concerns\HasBillingAddresses;
use Masterix21\Addressable\Models\Concerns\HasShippingAddresses;

class User extends Model
{
    use HasAddresses;          // ->addresses()
    use HasBillingAddresses;   // ->billingAddress(), ->billingAddresses()
    use HasShippingAddresses;  // ->shippingAddress(), ->shippingAddresses()
}
```

## Creating addresses

Prefer the helper methods over creating `Address` models directly — they set the
type flags automatically.

```php
$user->addAddress([...]);          // generic
$user->addBillingAddress([...]);   // sets is_billing
$user->addShippingAddress([...]);  // sets is_shipping

$user->primaryAddress();           // ?Address
```

## Primary address

Use `markPrimary()` / `unmarkPrimary()` — never set `is_primary` by hand. They
unmark sibling addresses of the same type, scoped to the same parent model, and
emit events (`AddressPrimaryMarked`, `BillingAddressPrimaryMarked`, etc.).

```php
$address->markPrimary();
$address->unmarkPrimary();
```

## Query scopes

```php
use Masterix21\Addressable\Models\Address;

Address::query()->primary()->billing()->first();
Address::query()->shipping()->get();
```

## Coordinates and distance

Coordinates use `MatanYadaev\EloquentSpatial\Objects\Point`. Always pass the SRID
from config. The `Address` model ships spatial query scopes — prefer them over
raw `whereDistanceSphere()` calls. Distances are always in **meters**.

```php
$address->coordinates = new Point(45.4642, 9.19, config('addressable.srid'));

$origin = new Point(45.4642, 9.19, config('addressable.srid'));

// Append a `distance` column (meters); pass an alias as the second argument
Address::query()->addDistanceTo($origin)->get();

// Filter addresses within a radius (meters)
Address::query()->withinRadius($origin, 10_000)->get();

// Order by distance; pass 'desc' as the second argument to reverse
Address::query()->orderByDistance($origin)->get();

// Nearest addresses: adds the `distance` column, orders ascending, optional limit
Address::query()->nearest($origin, 5)->get();
```

## Geocoding

`geocode()` resolves `coordinates` from the textual address; `reverseGeocode()`
fills the textual fields from `coordinates`. Neither persists — call `save()`
afterwards. Both emit the `AddressGeocoded` event on success.

```php
if ($address->geocode()) {
    $address->save();
}
```

Drivers are configured in `config/addressable.php` under `geocoding.drivers` and
tried in FIFO order (first hit wins). Set `geocoding.auto` to `true` to geocode
addresses without coordinates automatically on save.

## Metadata and display

Store extra data in the `meta` JSON column. Read the formatted address via the
`display_address` accessor; customize the format with `addressable.display_format`.

```php
$user->addAddress([
    'street_address1' => 'Via Roma 1',
    'city' => 'Milano',
    'meta' => ['phone' => '+39 02 1234567'],
]);

$address->display_address;
```
