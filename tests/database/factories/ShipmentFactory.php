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
        'label' => $faker->boolean ? $faker->streetName : null,
        'street_address1' => $faker->streetAddress,
        'street_address2' => $faker->streetAddress,
        'zip' => $faker->postcode,
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'country_code' => $faker->countryCode,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
