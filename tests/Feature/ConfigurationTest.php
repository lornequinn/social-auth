<?php

declare(strict_types=1);
use Laravel\Socialite\Contracts\Factory;
use LorneQuinn\SocialAuth\Drivers\GenericOAuth2Provider;
use LorneQuinn\SocialAuth\SocialAuthServiceProvider;

it('registers standard provider config into services', function () {
    config([
        'social-auth.providers' => [
            'google' => [
                'client_id' => 'google-id',
                'client_secret' => 'google-secret',
                'auth_url' => null,
                'token_url' => null,
                'user_url' => null,
                'scopes' => [],
            ],
        ],
    ]);

    // Re-boot the service provider to pick up the config
    app()->getProvider(SocialAuthServiceProvider::class)->boot();

    expect(config('services.google'))->toMatchArray([
        'client_id' => 'google-id',
        'client_secret' => 'google-secret',
    ]);
});

it('detects custom providers by presence of auth/token/user urls', function () {
    config([
        'social-auth.providers' => [
            'myapp' => [
                'client_id' => 'myapp-id',
                'client_secret' => 'myapp-secret',
                'auth_url' => 'https://myapp.com/oauth/authorize',
                'token_url' => 'https://myapp.com/oauth/token',
                'user_url' => 'https://myapp.com/api/user',
                'scopes' => [],
            ],
        ],
    ]);

    app()->getProvider(SocialAuthServiceProvider::class)->boot();

    expect(config('services.myapp'))->toMatchArray([
        'client_id' => 'myapp-id',
        'client_secret' => 'myapp-secret',
    ]);

    // Custom driver should be registered — calling driver() should not throw
    $driver = app(Factory::class)->driver('myapp');
    expect($driver)->toBeInstanceOf(GenericOAuth2Provider::class);
});

it('sets redirect url using route prefix', function () {
    config([
        'social-auth.route_prefix' => 'oauth',
        'social-auth.providers' => [
            'github' => [
                'client_id' => 'gh-id',
                'client_secret' => 'gh-secret',
                'auth_url' => null,
                'token_url' => null,
                'user_url' => null,
                'scopes' => [],
            ],
        ],
    ]);

    app()->getProvider(SocialAuthServiceProvider::class)->boot();

    expect(config('services.github.redirect'))->toBe('/oauth/github/callback');
});
