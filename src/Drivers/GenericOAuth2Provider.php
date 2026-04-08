<?php

declare(strict_types=1);

namespace LorneQuinn\SocialAuth\Drivers;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class GenericOAuth2Provider extends AbstractProvider
{
    /** @param array<string, mixed> $guzzle */
    public function __construct(
        Request $request,
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        protected string $authUrl,
        protected string $tokenUrl,
        protected string $userUrl,
        array $guzzle = [],
    ) {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->authUrl, $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    /** @return array<string, mixed> */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get($this->userUrl, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /** @param array<string, mixed> $user */
    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? $user['sub'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? $user['picture'] ?? null,
            'nickname' => $user['nickname'] ?? $user['name'] ?? null,
        ]);
    }
}
