<?php

namespace Masterix21\Addressable\Database\Factories;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Masterix21\Addressable\Models\Address;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'is_primary' => $this->faker->boolean,
            'is_billing' => $this->faker->boolean,
            'is_shipping' => $this->faker->boolean,
            'label' => $this->faker->boolean ? $this->faker->streetName : null,
            'street_address1' => $this->faker->streetAddress,
            'street_address2' => $this->faker->streetAddress,
            'zip' => $this->faker->postcode,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->countryCode,
            'coordinates' => ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude],
        ];
    }

    public function addressable(Model $model): Factory
    {
        return $this->state(function (array $attributes) use ($model) {
            return [
                'addressable_type' => $model::class,
                'addressable_id' => $model->getKey(),
            ];
        });
    }

    public function primary(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => true,
            ];
        });
    }

    public function billing(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_billing' => true,
            ];
        });
    }

    public function shipping(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_shipping' => true,
            ];
        });
    }
}
