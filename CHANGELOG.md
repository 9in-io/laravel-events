# CHANGELOG

## 3.0.0 - 2026-11-10

Breaking: reads 9in's snake_case payloads (the 9in release of 2026-11-10). See [MIGRATION.md](MIGRATION.md).

* `EventFactory` maps by `event_type` (was `type`); `type()` and `createdAt()` read `event_type` and `created_at`.
* `userId()` reads `data.user_id` (adapter) or `data.user.id` (webhook); webhooks no longer send `data.userId`.
* `OtpRequested::expiresAt()` reads `expires_at`; `TokenRevoked::tokenType()` / `clientId()` read
  `token_type` / `client_id`.

## 2.0.1 - 2026-11-09

Same code as 2.0.0, released again under a new version because the 2.0.0 tag was moved to a
rewritten commit after Packagist had published it (published versions cannot change).

## 2.0.0 - 2026-11-09

Rebranded as **9in** (`9in/laravel-events`, namespace `Nine\Laravel\Events`). See [MIGRATION.md](MIGRATION.md).

* One set of event classes for adapter notifications and webhooks (`NineEvent` with `fromAdapter()` / `fromWebhook()`).
* New: `TokenRevoked` (`token.revoked`) and `sid()`; `session.revoked` webhooks map to `SessionsRevoked`.
* New: `OtpRequested`, `UserDeleted`, `PasswordChanged`, `SessionsRevoked`, `UserLoggedIn`, `LoginFailed`, `MfaEnabled`, `MfaDisabled`, `WebhookTest`, `EventReceived`, `EventFactory`.
* Removed: `OTPSent`, `TokenDeleted`, `SafetyReset`, `UserAuthenticated`.
* No dependency on the PHP SDK; PHP 8.1+.
* Unit tests (phpunit).

## 1.0.0

* Initial SSOfy release.
