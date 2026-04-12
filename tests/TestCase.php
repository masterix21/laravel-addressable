<?php

namespace Masterix21\Addressable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Masterix21\Addressable\AddressableServiceProvider;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithLaravelMigrations;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                return 'Masterix21\\Addressable\\Database\\Factories\\'.class_basename($modelName).'Factory';
            }
        );

        $this->migrateDb();
    }

    protected function getPackageProviders($app): array
    {
        return [
            AddressableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'username' => env('DB_USERNAME', 'root'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'password' => env('DB_PASSWORD'),
            'database' => env('DB_NAME', 'test'),
        ]);
    }

    public function migrateDb(): void
    {
        /*$migration = include __DIR__.'/database/migrations/2014_10_12_000000_create_users_table.php';
        $migration->up();*/

        $migration = include __DIR__.'/../database/migrations/create_addressable_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/add_meta_to_addressable_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/add_spatial_index_to_addressable_table.php.stub';
        $migration->up();

        Schema::create('soft_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
