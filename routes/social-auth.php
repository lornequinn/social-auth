<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LorneQuinn\SocialAuth\Http\Controllers\SocialAuthController;

$prefix = config('social-auth.route_prefix', 'auth/social');
$middleware = config('social-auth.middleware', ['web']);

Route::middleware($middleware)
    ->prefix($prefix)
    ->group(function () {
        Route::get('{provider}/redirect', [SocialAuthController::class, 'redirect'])
            ->name('social-auth.redirect');

        Route::get('{provider}/callback', [SocialAuthController::class, 'callback'])
            ->name('social-auth.callback');
    });
