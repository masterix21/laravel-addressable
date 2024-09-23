<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Masterix21\Addressable\Concerns\UsesAddressableConfig;

return new class extends Migration
{
    use UsesAddressableConfig;

    public function up(): void
    {
        Schema::create($this->addressesDatabaseTable(), function (Blueprint $table) {
            $table->id();

            $table->morphs('addressable');
            $table->string('label')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_billing')->default(false);
            $table->boolean('is_shipping')->default(false);
            $table->string('street_address1')->nullable();
            $table->string('street_address2')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 4)->nullable();
            $table->geography('coordinates', 'point', config('addressable.srid'))->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->addressesDatabaseTable());
    }
};
