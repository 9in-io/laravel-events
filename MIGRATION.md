# Migrating

## 2.x → 3.x

3.0 reads 9in's snake_case payloads (the 9in release of 2026-11-10). Upgrade together with
`9in/laravel` 4.x and `9in/laravel-server` 4.x. The accessors keep their names; what they read changed:

| Accessor | 2.x read | 3.x reads |
|---|---|---|
| `type()` | `type` | `event` |
| `createdAt()` | `createdAt` | `created_at` |
| `userId()` | `data.userId` | `data.user_id` (adapter) or `data.user.id` (webhook) |
| `OtpRequested::expiresAt()` | `data.expiresAt` | `data.expires_at` |
| `TokenRevoked::tokenType()`, `clientId()` | `data.type`, `data.clientId` | `data.token_type`, `data.client_id` |
| `user()` on `UserDeleted` | null | the user as it was before the deletion |

Code that reads `$event->event` or `$event->data()` directly needs the same renames.

## From `ssofy/laravel-events` 1.x to `9in/laravel-events` 2.x

SSOfy is now [9in](https://9in.io). 2.0 is a new major version under a new package name.

**Upgrading is required to use 9in.** `ssofy/laravel-events` 1.x only talks to the SSOfy service (its events come from `ssofy/laravel` and `ssofy/laravel-server` 2.x). That service has shut down (ssofy.com has lapsed), and 9in does not serve the SSOfy v1 protocol, so 1.x does not work against 9in. The old versions stay published but are no longer maintained. Move to 2.x as part of moving the app to 9in.
Upgrade together with `9in/laravel` 3.x and `9in/laravel-server` 3.x.

```bash
composer remove ssofy/laravel-events
composer require 9in/laravel-events:^2.0
```

Namespace: `SSOfy\Laravel\Events\…` → `Nine\Laravel\Events\…`. The package no longer
depends on the PHP SDK, and PHP 8.1+ is required.

| 1.x | 2.x |
|---|---|
| `OTPSent($option)`: SSOfy sent a code through your server | `OtpRequested`: 9in asks you to deliver `code()` to `to()` |
| `UserCreated($user, $ip)` | `UserCreated` (`userId()`, `user()` for webhooks) |
| `UserUpdated($user, $ip)` | `UserUpdated` (`data()['fields']` for adapter events) |
| `UserAuthenticated` | `UserLoggedIn` (webhook `user.login`) |
| `TokenDeleted($token)` | `TokenRevoked` (`token.revoked` webhook: `tokenType()`, `jti()`, `sid()`, `clientId()`); SSOfy's `auth/token` itself is replaced by single-use login links |
| `SafetyReset` | `SessionsRevoked` |
| | New: `UserDeleted`, `PasswordChanged`, `LoginFailed`, `MfaEnabled`, `MfaDisabled`, `WebhookTest`, `TokenRevoked`, `EventReceived`; `SessionsRevoked` also covers `session.revoked` webhooks (`sid()`) |

Events no longer carry a `UserEntity` and an IP address. They wrap the 9in event payload
(`$event->event`) and expose typed accessors.
