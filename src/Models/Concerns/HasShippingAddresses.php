<?php

namespace Masterix21\Addressable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;

trait HasShippingAddresses
{
    use UsesAddressableConfig;

    public static function bootHasShippingAddresses(): void
    {
        static::deleted(function (Model $deletedModel) {
            $deletedModel->shippingAddresses()->delete();
        });
    }

    public function shippingAddress(): MorphOne
    {
        return $this->morphOne($this->addressModel(), 'addressable')
            ->where('is_primary', true)
            ->where('is_shipping', true);
    }

    public function shippingAddresses(): MorphMany
    {
        return $this->morphMany($this->addressModel(), 'addressable')
            ->where('is_shipping', true);
    }
}
