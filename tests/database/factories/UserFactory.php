<?php

use \Faker\Generator;
use Masterix21\Addressable\Tests\TestClasses\User;

/* @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
