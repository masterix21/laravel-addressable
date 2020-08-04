<?php

namespace Masterix21\LaravelAddressable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Masterix21\LaravelAddressable\LaravelAddressableServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAddressableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        /*
        include_once __DIR__.'/../database/migrations/create_laravel_addressable_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
