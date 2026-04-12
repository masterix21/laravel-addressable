<?php

namespace Masterix21\Addressable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;
use Masterix21\Addressable\Models\Address;

trait HasBillingAddresses
{
    use UsesAddressableConfig;

    public static function bootHasBillingAddresses(): void
    {
        if (in_array(HasAddresses::class, class_uses_recursive(static::class))) {
            return;
        }

        static::deleted(function (Model $deletedModel) {
            if (method_exists($deletedModel, 'isForceDeleting') && ! $deletedModel->isForceDeleting()) {
                return;
            }

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

    public function addBillingAddress(array $data): Address
    {
        return $this->billingAddresses()->create(array_merge($data, ['is_billing' => true]));
    }
}
