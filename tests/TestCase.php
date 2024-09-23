<?php

namespace Masterix21\Addressable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Masterix21\Addressable\AddressableServiceProvider;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithLaravelMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                return 'Masterix21\\Addressable\\Database\\Factories\\'.class_basename($modelName).'Factory';
            }
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            AddressableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $this->migrateDb();
    }

    public function migrateDb(): void
    {
        /*$migration = include __DIR__.'/database/migrations/2014_10_12_000000_create_users_table.php';
        $migration->up();*/

        $migration = include __DIR__.'/../database/migrations/create_addressable_table.php.stub';
        $migration->up();
    }
}
