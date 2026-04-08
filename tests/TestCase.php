<?php

declare(strict_types=1);

namespace LorneQuinn\SocialAuth\Tests;

use Illuminate\Database\Eloquent\Model;
use Laravel\Socialite\SocialiteServiceProvider;
use LorneQuinn\SocialAuth\SocialAuthServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::unguard();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SocialiteServiceProvider::class,
            SocialAuthServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }
}
