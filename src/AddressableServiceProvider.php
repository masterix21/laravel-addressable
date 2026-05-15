<?php

namespace Masterix21\Addressable;

use Illuminate\Support\ServiceProvider;
use Masterix21\Addressable\Geocoding\ChainGeocoder;
use Masterix21\Addressable\Geocoding\Contracts\Geocoder;
use Masterix21\Addressable\Observers\AddressObserver;

class AddressableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/addressable.php' => config_path('addressable.php'),
            ], 'config');

            if (! class_exists('CreateAddressableTable')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_addressable_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_addressable_table.php'),
                ], 'migrations');
            }

            $this->publishes([
                __DIR__.'/../database/migrations/add_meta_to_addressable_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_add_meta_to_addressable_table.php'),
            ], 'addressable-meta-migration');

            $this->publishes([
                __DIR__.'/../database/migrations/add_spatial_index_to_addressable_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_add_spatial_index_to_addressable_table.php'),
            ], 'addressable-spatial-index-migration');
        }

        config('addressable.models.address')::observe(AddressObserver::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/addressable.php', 'addressable');

        $this->app->singleton(Geocoder::class, function () {
            $geocoding = config('addressable.geocoding', []);
            $shared = [
                'srid' => config('addressable.srid'),
                'user_agent' => $geocoding['user_agent'] ?? 'laravel-addressable',
            ];

            $drivers = collect($geocoding['drivers'] ?? [])
                ->map(function (array $driverConfig) use ($shared) {
                    $class = $driverConfig['class'];

                    return new $class(array_merge($shared, $driverConfig));
                })
                ->values()
                ->all();

            return new ChainGeocoder($drivers);
        });
    }
}
