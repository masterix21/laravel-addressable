<?php

namespace Masterix21\Addressable;

use Illuminate\Support\ServiceProvider;

class AddressableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/addressable.php' => config_path('addressable.php'),
            ], 'config');

            if (! class_exists('CreateAddressableTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_addressable_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_addressable_table.php'),
                ], 'migrations');
            }
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'addressable');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/addressable.php', 'addressable');
    }
}
