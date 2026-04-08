# Social Auth

Config-driven Socialite OAuth client for Laravel. Add OAuth login via env vars — no boilerplate.

Supports standard Socialite providers (Google, GitHub, Facebook, etc.) and custom OAuth2 servers with a generic driver.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Installation

```bash
composer require lornequinn/social-auth
```

The service provider is auto-discovered.

Publish the config (optional):

```bash
php artisan vendor:publish --tag=social-auth-config
```

## Configuration

Everything is driven by env vars. No config files to edit for the common case.

### Standard Providers

For providers with built-in Socialite drivers (Google, GitHub, Facebook, etc.):

```env
LQ_SOCIAL_PROVIDERS=google,github

LQ_SOCIAL_GOOGLE_CLIENT_ID=your-google-client-id
LQ_SOCIAL_GOOGLE_CLIENT_SECRET=your-google-client-secret

LQ_SOCIAL_GITHUB_CLIENT_ID=your-github-client-id
LQ_SOCIAL_GITHUB_CLIENT_SECRET=your-github-client-secret
```

### Custom OAuth2 Providers

For any OAuth2 server (e.g. a Laravel Passport instance, a corporate SSO, or any other OAuth2-compliant server):

```env
LQ_SOCIAL_PROVIDERS=myapp

LQ_SOCIAL_MYAPP_CLIENT_ID=your-client-id
LQ_SOCIAL_MYAPP_CLIENT_SECRET=your-client-secret
LQ_SOCIAL_MYAPP_AUTH_URL=https://auth.example.com/oauth/authorize
LQ_SOCIAL_MYAPP_TOKEN_URL=https://auth.example.com/oauth/token
LQ_SOCIAL_MYAPP_USER_URL=https://auth.example.com/api/user
```

A provider is treated as custom when `AUTH_URL`, `TOKEN_URL`, and `USER_URL` are all set.

### Mixed

Standard and custom providers can be combined:

```env
LQ_SOCIAL_PROVIDERS=myapp,google
```

### Additional Options

```env
# Route prefix (default: auth/social)
LQ_SOCIAL_ROUTE_PREFIX=auth/social

# User model (default: App\Models\User)
LQ_SOCIAL_USER_MODEL=App\Models\User

# Column to match existing users (default: email)
LQ_SOCIAL_MATCH_BY=email

# Redirect after login (default: /dashboard)
LQ_SOCIAL_REDIRECT_TO=/dashboard

# Scopes (comma-separated, per provider)
LQ_SOCIAL_GOOGLE_SCOPES=openid,profile,email
```

## Routes

Routes are registered automatically for each configured provider:

| Route | Name | Purpose |
|-------|------|---------|
| `GET /{prefix}/{provider}/redirect` | `social-auth.redirect` | Redirect to OAuth provider |
| `GET /{prefix}/{provider}/callback` | `social-auth.callback` | Handle OAuth callback |

With the default prefix, Google login would be at `/auth/social/google/redirect`.

Generate URLs in your templates:

```blade
<a href="{{ route('social-auth.redirect', 'google') }}">Login with Google</a>
```

## User Resolution

On callback, the package will:

1. Fetch the authenticated user from the OAuth provider
2. Find or create a local user by matching the configured column (default: `email`)
3. Log the user in with `remember: true`
4. Redirect to the intended URL (or the configured `redirect_to`)

## License

MIT
