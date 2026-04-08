<?php

declare(strict_types=1);

return [

    'route_prefix' => env('LQ_SOCIAL_ROUTE_PREFIX', 'auth/social'),

    'middleware' => ['web'],

    'user_model' => env('LQ_SOCIAL_USER_MODEL', 'App\\Models\\User'),

    'match_by' => env('LQ_SOCIAL_MATCH_BY', 'email'),

    'redirect_to' => env('LQ_SOCIAL_REDIRECT_TO', '/dashboard'),

    'providers' => (function () {
        $providers = array_filter(
            array_map('trim', explode(',', env('LQ_SOCIAL_PROVIDERS', '')))
        );

        $config = [];

        foreach ($providers as $provider) {
            $prefix = 'LQ_SOCIAL_'.strtoupper($provider);

            $config[$provider] = [
                'client_id' => env("{$prefix}_CLIENT_ID"),
                'client_secret' => env("{$prefix}_CLIENT_SECRET"),
                'auth_url' => env("{$prefix}_AUTH_URL"),
                'token_url' => env("{$prefix}_TOKEN_URL"),
                'user_url' => env("{$prefix}_USER_URL"),
                'scopes' => array_filter(
                    array_map('trim', explode(',', env("{$prefix}_SCOPES", '')))
                ),
            ];
        }

        return $config;
    })(),

];
