# Migrating

## 2.x → 3.x

3.0 reads 9in's snake_case payloads (the 9in release of 2026-11-10). Upgrade together with
`9in/laravel` 4.x and `9in/laravel-server` 4.x. The accessors keep their names; what they read changed:

| Accessor | 2.x read | 3.x reads |
|---|---|---|
| `type()` | `type` | `event_type` |
| `createdAt()` | `createdAt` | `created_at` |
| `userId()` | `data.userId` | `data.user_id` (adapter) or `data.user.id` (webhook) |
| `OtpRequested::expiresAt()` | `data.expiresAt` | `data.expires_at` |
| `TokenRevoked::tokenType()`, `clientId()` | `data.type`, `data.clientId` | `data.token_type`, `data.client_id` |
| `user()` on `UserDeleted` | null | the user as it was before the deletion |

Code that reads `$event->event` or `$event->data()` directly needs the same renames.
