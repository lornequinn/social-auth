<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('registers redirect and callback routes', function () {
    expect(Route::has('social-auth.redirect'))->toBeTrue();
    expect(Route::has('social-auth.callback'))->toBeTrue();
});

it('generates correct route urls', function () {
    $redirectUrl = route('social-auth.redirect', ['provider' => 'google']);
    $callbackUrl = route('social-auth.callback', ['provider' => 'google']);

    expect($redirectUrl)->toContain('/auth/social/google/redirect');
    expect($callbackUrl)->toContain('/auth/social/google/callback');
});
