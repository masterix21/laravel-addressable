<?php

namespace Masterix21\Addressable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;

trait HasBillingAddresses
{
    use UsesAddressableConfig;

    public static function bootHasBillingAddresses(): void
    {
        static::deleted(function (Model $deletedModel) {
            $deletedModel->billingAddresses()->delete();
        });
    }

    public function billingAddress(): MorphOne
    {
        return $this->morphOne($this->addressModel(), 'addressable')
            ->where('is_primary', true)
            ->where('is_billing', true);
    }

    public function billingAddresses(): MorphMany
    {
        return $this->morphMany($this->addressModel(), 'addressable')
            ->where('is_billing', true);
    }
}
