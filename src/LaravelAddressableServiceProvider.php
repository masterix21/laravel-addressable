<?php

namespace Masterix21\LaravelAddressable;

use Illuminate\Support\ServiceProvider;
use Masterix21\LaravelAddressable\Commands\LaravelAddressableCommand;

class LaravelAddressableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-addressable.php' => config_path('laravel-addressable.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/laravel-addressable'),
            ], 'views');

            if (! class_exists('CreatePackageTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_laravel_addressable_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_laravel_addressable_table.php'),
                ], 'migrations');
            }

            $this->commands([
                LaravelAddressableCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-addressable');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-addressable.php', 'laravel-addressable');
    }
}
