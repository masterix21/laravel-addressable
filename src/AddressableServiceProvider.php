<?php

namespace Masterix21\Addressable;

use Illuminate\Support\ServiceProvider;

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
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/addressable.php', 'addressable');
    }
}
