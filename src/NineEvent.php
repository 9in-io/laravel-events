<?php

namespace Nine\Laravel\Events;

/**
 * An event from 9in, either an adapter notification (`POST <adapter>/event`, handled by
 * 9in/laravel-server) or a webhook (handled by 9in/laravel). The original payload is in
 * `$event`; `$source` says which channel delivered it.
 */
abstract class NineEvent
{
    public const SOURCE_ADAPTER = 'adapter';
    public const SOURCE_WEBHOOK = 'webhook';

    /** @var array<string, mixed> */
    public $event;

    /** @var string adapter | webhook */
    public $source;

    /**
     * @param array<string, mixed> $event
     */
    public function __construct(array $event, string $source)
    {
        $this->event  = $event;
        $this->source = $source;
    }

    /** Event id; the same on every retry, so you can ignore duplicates. */
    public function id(): string
    {
        return (string) ($this->event['id'] ?? '');
    }

    /** The event name, e.g. `user.created` (the payload's `event`). */
    public function type(): string
    {
        return (string) ($this->event['event'] ?? '');
    }

    /** When the event happened (the payload's `created_at`, ISO 8601 UTC). */
    public function createdAt(): ?\DateTimeImmutable
    {
        return isset($this->event['created_at']) ? new \DateTimeImmutable((string) $this->event['created_at']) : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return isset($this->event['data']) && is_array($this->event['data']) ? $this->event['data'] : [];
    }

    /**
     * The user the event is about: your own user id for adapter events (`data.user_id`), the
     * 9in user id for webhooks (`data.user.id`).
     */
    public function userId(): ?string
    {
        $data = $this->data();
        $id   = $data['user_id'] ?? (isset($data['user']) && is_array($data['user']) ? ($data['user']['id'] ?? null) : null);
        return $id === null ? null : (string) $id;
    }

    /** Session id, for session and token events. */
    public function sid(): ?string
    {
        $sid = $this->data()['sid'] ?? null;
        return $sid === null ? null : (string) $sid;
    }

    /**
     * The 9in user the event is about, as the Management API returns it (webhooks only; for
     * `user.deleted`, the user as it was just before the deletion).
     *
     * @return array<string, mixed>|null
     */
    public function user(): ?array
    {
        $user = $this->data()['user'] ?? null;
        return is_array($user) ? $user : null;
    }

    /** Tenant id (`tnt_…`), webhooks only. */
    public function tenant(): ?string
    {
        return isset($this->event['tenant']) ? (string) $this->event['tenant'] : null;
    }

    /** production | sandbox, webhooks only. */
    public function environment(): ?string
    {
        return isset($this->event['environment']) ? (string) $this->event['environment'] : null;
    }

    public function fromAdapter(): bool
    {
        return $this->source === self::SOURCE_ADAPTER;
    }

    public function fromWebhook(): bool
    {
        return $this->source === self::SOURCE_WEBHOOK;
    }
}
