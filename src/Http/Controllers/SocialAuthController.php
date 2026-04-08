<?php

declare(strict_types=1);

namespace LorneQuinn\SocialAuth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class SocialAuthController extends Controller
{
    public function redirect(Request $request, Socialite $socialite, string $provider): SymfonyRedirect
    {
        return $socialite->driver($provider)->redirect();
    }

    public function callback(Request $request, Socialite $socialite, string $provider): RedirectResponse
    {
        $socialiteUser = $socialite->driver($provider)->user();

        $model = config('social-auth.user_model');
        $matchBy = config('social-auth.match_by', 'email');

        $user = $model::firstOrCreate(
            [$matchBy => $socialiteUser->getEmail()],
            [
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
            ]
        );

        Auth::login($user, remember: true);

        return redirect()->intended(config('social-auth.redirect_to', '/dashboard'));
    }
}
