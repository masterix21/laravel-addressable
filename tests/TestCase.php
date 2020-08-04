<?php

namespace Masterix21\Addressable\Tests;

use Masterix21\Addressable\AddressableServiceProvider;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithLaravelMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            AddressableServiceProvider::class,
        ];
    }

    public function migrateDb()
    {
        include_once __DIR__.'/database/migrations/2014_10_12_000000_create_users_table.php';
        (new \CreateUsersTable())->up();

        include_once __DIR__.'/../database/migrations/create_addressable_table.php.stub';
        (new \CreateAddressableTable())->up();
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $this->migrateDb();
    }
}
