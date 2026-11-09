<?php

namespace Nine\Laravel\Events;

/** Maps 9in event types to event classes. */
final class EventFactory
{
    /** @var array<string, class-string<NineEvent>> */
    public const MAP = [
        'otp.requested'         => OtpRequested::class,
        'user.created'          => UserCreated::class,
        'user.updated'          => UserUpdated::class,
        'user.deleted'          => UserDeleted::class,
        'user.password_changed' => PasswordChanged::class,
        'session.revoked'       => SessionsRevoked::class,
        'user.sessions_revoked' => SessionsRevoked::class,
        'token.revoked'         => TokenRevoked::class,
        'user.login'            => UserLoggedIn::class,
        'user.login_failed'     => LoginFailed::class,
        'user.mfa_enabled'      => MfaEnabled::class,
        'user.mfa_disabled'     => MfaDisabled::class,
        'webhook.test'          => WebhookTest::class,
    ];

    /**
     * The specific event for `$event['type']`, or null for types this version does not know.
     *
     * @param array<string, mixed> $event
     */
    public static function make(array $event, string $source): ?NineEvent
    {
        $class = self::MAP[(string) ($event['type'] ?? '')] ?? null;
        return $class ? new $class($event, $source) : null;
    }

    /**
     * Everything to dispatch for one payload: the specific event (when known) followed by
     * EventReceived.
     *
     * @param array<string, mixed> $event
     * @return NineEvent[]
     */
    public static function all(array $event, string $source): array
    {
        $specific = self::make($event, $source);
        return array_values(array_filter([$specific, new EventReceived($event, $source)]));
    }
}
