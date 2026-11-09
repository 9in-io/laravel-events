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

    public function type(): string
    {
        return (string) ($this->event['type'] ?? '');
    }

    public function createdAt(): ?\DateTimeImmutable
    {
        return isset($this->event['createdAt']) ? new \DateTimeImmutable((string) $this->event['createdAt']) : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return isset($this->event['data']) && is_array($this->event['data']) ? $this->event['data'] : [];
    }

    /**
     * The user the event is about: your own user id for adapter events, the 9in user id
     * for webhooks.
     */
    public function userId(): ?string
    {
        $id = $this->data()['userId'] ?? null;
        return $id === null ? null : (string) $id;
    }

    /** Session id, for session and token events. */
    public function sid(): ?string
    {
        $sid = $this->data()['sid'] ?? null;
        return $sid === null ? null : (string) $sid;
    }

    /**
     * Snapshot of the 9in user (webhooks only; null once the user is deleted).
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
