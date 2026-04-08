<?php

declare(strict_types=1);

namespace LorneQuinn\SocialAuth;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager;
use LorneQuinn\SocialAuth\Drivers\GenericOAuth2Provider;

class SocialAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/social-auth.php', 'social-auth');
    }

    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureProviders();
        $this->configureRoutes();
    }

    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/social-auth.php' => config_path('social-auth.php'),
            ], 'social-auth-config');
        }
    }

    protected function configureProviders(): void
    {
        $providers = config('social-auth.providers', []);

        /** @var SocialiteManager $socialite */
        $socialite = $this->app->make(Factory::class);

        foreach ($providers as $name => $provider) {
            $isCustom = ! empty($provider['auth_url'])
                && ! empty($provider['token_url'])
                && ! empty($provider['user_url']);

            $routePrefix = config('social-auth.route_prefix', 'auth/social');

            config(["services.{$name}" => [
                'client_id' => $provider['client_id'],
                'client_secret' => $provider['client_secret'],
                'redirect' => "/{$routePrefix}/{$name}/callback",
            ]]);

            if ($isCustom) {
                $socialite->extend($name, function () use ($name, $provider) {
                    return $this->buildCustomProvider($name, $provider);
                });
            }
        }
    }

    /** @param array<string, mixed> $provider */
    protected function buildCustomProvider(string $name, array $provider): GenericOAuth2Provider
    {
        $config = config("services.{$name}");

        return (new GenericOAuth2Provider(
            $this->app->make('request'),
            $config['client_id'],
            $config['client_secret'],
            $config['redirect'],
            $provider['auth_url'],
            $provider['token_url'],
            $provider['user_url'],
        ))->scopes($provider['scopes'] ?? []);
    }

    protected function configureRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/social-auth.php');
    }
}
