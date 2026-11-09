9in Laravel Events
==================

Event classes shared by the [9in](https://9in.io) Laravel packages:

- [`9in/laravel-server`](https://github.com/9in-io/laravel-server) dispatches them for
  **adapter notifications** (`POST <adapter>/event`), for apps that keep users in their own
  database.
- [`9in/laravel`](https://github.com/9in-io/laravel) dispatches them for **webhooks**, for apps
  whose users are stored by 9in.

Both packages pull this one in. Require it directly only when you want the classes without
either package:

```bash
composer require 9in/laravel-events:^2.0
```

## Events

Every event extends `Nine\Laravel\Events\NineEvent` and keeps the original payload in
`$event->event`. Accessors: `id()`, `type()`, `createdAt()`, `data()`, `userId()`, `sid()`, `user()`
(webhooks), `tenant()`, `environment()`, `fromAdapter()`, `fromWebhook()`.

| Class | 9in event type | Adapter | Webhook |
|---|---|:-:|:-:|
| `OtpRequested` | `otp.requested`: deliver `code()` to `to()` by `channel()` | ✓ | |
| `UserCreated` | `user.created` | ✓ | ✓ |
| `UserUpdated` | `user.updated` (adapter events list the `fields`) | ✓ | ✓ |
| `UserDeleted` | `user.deleted` | | ✓ |
| `PasswordChanged` | `user.password_changed` | ✓ | ✓ |
| `SessionsRevoked` | `session.revoked` (one session: `sid()`) / `user.sessions_revoked` (all of a user's) | ✓ | ✓ |
| `TokenRevoked` | `token.revoked`: `tokenType()`, `jti()` (access tokens), `sid()`, `clientId()` | | ✓ |
| `UserLoggedIn` | `user.login` | | ✓ |
| `LoginFailed` | `user.login_failed` | | ✓ |
| `MfaEnabled`, `MfaDisabled` | `user.mfa_enabled`, `user.mfa_disabled` | | ✓ |
| `WebhookTest` | `webhook.test` | | ✓ |
| `EventReceived` | every event, including types added later | ✓ | ✓ |

```php
use Illuminate\Support\Facades\Event;
use Nine\Laravel\Events\PasswordChanged;

Event::listen(PasswordChanged::class, function (PasswordChanged $event) {
    // $event->userId(): your user id (adapter) or the 9in user id (webhook)
});
```

Event ids stay the same on every retry, so listeners can ignore duplicates by `id()`.

`EventFactory::make($payload, $source)` returns the event for a payload;
`EventFactory::all()` returns it followed by `EventReceived`.

Upgrading from `ssofy/laravel-events` 1.x? Read [MIGRATION.md](MIGRATION.md).

## License

The MIT License (MIT). See [LICENSE](LICENSE). 9in and SSOfy are by [Cubelet Ltd](https://cubelet.co.uk).
