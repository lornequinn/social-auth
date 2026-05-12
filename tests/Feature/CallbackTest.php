<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use LorneQuinn\SocialAuth\SocialAuthServiceProvider;

beforeEach(function () {
    Schema::dropIfExists('users');
    Schema::create('users', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
});

it('sets email_verified_at when creating a user via social callback', function () {
    config([
        'social-auth.user_model' => User::class,
        'social-auth.match_by' => 'email',
        'social-auth.providers' => [
            'google' => [
                'client_id' => 'gid',
                'client_secret' => 'gsecret',
                'auth_url' => null,
                'token_url' => null,
                'user_url' => null,
                'scopes' => [],
            ],
        ],
    ]);

    app()->getProvider(SocialAuthServiceProvider::class)->boot();

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getEmail')->andReturn('alice@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Alice');

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    $factory = Mockery::mock(Factory::class);
    $factory->shouldReceive('driver')->with('google')->andReturn($provider);

    $this->app->instance(Factory::class, $factory);

    $response = $this->get(route('social-auth.callback', ['provider' => 'google']));

    $response->assertRedirect();

    $user = User::query()->where('email', 'alice@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->email_verified_at)->not->toBeNull();
});
