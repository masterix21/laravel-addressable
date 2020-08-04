<?php

use \Faker\Generator;
use Masterix21\Addressable\Models\Address;
use Masterix21\Addressable\Tests\TestClasses\User;

/* @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Address::class, function (Generator $faker) {
    return [
        'addressable_type' => User::class,
        'addressable_id' => User::inRandomOrder()->first()->id,
        'is_primary' => $faker->boolean,
        'is_billing' => $faker->boolean,
        'is_shipping' => $faker->boolean,
        'address' => $faker->address,
        'secondary_address' => $faker->secondaryAddress,
        'street_address' => $faker->streetAddress,
        'street_name' => $faker->streetName,
        'street_suffix' => $faker->streetSuffix,
        'building_number' => $faker->buildingNumber,
        'city' => $faker->city,
        'city_prefix' => ,
        'city_suffix' => $faker->citySuffix,
        'postcode',
        'state',
        'state_abbr',
        'country',
        'country_abbr',
        'latitude',
        'longitude',
    ];
});
