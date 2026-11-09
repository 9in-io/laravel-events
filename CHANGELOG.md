# CHANGELOG

## 2.0.0 - unreleased

Rebranded as **9in** (`9in/laravel-events`, namespace `Nine\Laravel\Events`). See [MIGRATION.md](MIGRATION.md).

* One set of event classes for adapter notifications and webhooks (`NineEvent` with `fromAdapter()` / `fromWebhook()`).
* New: `TokenRevoked` (`token.revoked`) and `sid()`; `session.revoked` webhooks map to `SessionsRevoked`.
* New: `OtpRequested`, `UserDeleted`, `PasswordChanged`, `SessionsRevoked`, `UserLoggedIn`, `LoginFailed`, `MfaEnabled`, `MfaDisabled`, `WebhookTest`, `EventReceived`, `EventFactory`.
* Removed: `OTPSent`, `TokenDeleted`, `SafetyReset`, `UserAuthenticated`.
* No dependency on the PHP SDK; PHP 8.1+.
* Unit tests (phpunit).

## 1.0.0

* Initial SSOfy release.
