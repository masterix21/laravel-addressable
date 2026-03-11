<?php

namespace Masterix21\Addressable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;
use Masterix21\Addressable\Models\Address;

trait HasAddresses
{
    use UsesAddressableConfig;

    public static function bootHasAddresses(): void
    {
        static::deleted(function (Model $deletedModel) {
            $deletedModel->addresses()->delete();
        });
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany($this->addressModel(), 'addressable');
    }

    public function addAddress(array $data): Address
    {
        return $this->addresses()->create($data);
    }

    public function primaryAddress(): ?Address
    {
        return $this->addresses()->where('is_primary', true)->first();
    }
}
