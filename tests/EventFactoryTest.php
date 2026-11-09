<?php

namespace Nine\Laravel\Events\Tests;

use Nine\Laravel\Events\EventFactory;
use Nine\Laravel\Events\EventReceived;
use Nine\Laravel\Events\NineEvent;
use Nine\Laravel\Events\OtpRequested;
use Nine\Laravel\Events\SessionsRevoked;
use Nine\Laravel\Events\TokenRevoked;
use Nine\Laravel\Events\UserCreated;
use Nine\Laravel\Events\UserUpdated;
use PHPUnit\Framework\TestCase;

final class EventFactoryTest extends TestCase
{
    public function testAdapterOtpEvent(): void
    {
        $event = EventFactory::make([
            'id'        => 'evt_1',
            'type'      => 'otp.requested',
            'createdAt' => '2026-11-09T10:00:00.000Z',
            'data'      => ['userId' => '42', 'channel' => 'sms', 'to' => '+447700900000', 'code' => '123456', 'purpose' => 'login', 'expiresAt' => '2026-11-09T10:05:00.000Z'],
        ], NineEvent::SOURCE_ADAPTER);

        $this->assertInstanceOf(OtpRequested::class, $event);
        $this->assertTrue($event->fromAdapter());
        $this->assertSame('evt_1', $event->id());
        $this->assertSame('42', $event->userId());
        $this->assertSame('123456', $event->code());
        $this->assertSame('sms', $event->channel());
        $this->assertSame('+447700900000', $event->to());
        $this->assertSame('2026-11-09T10:05:00+00:00', $event->expiresAt()->format(DATE_ATOM));
    }

    public function testWebhookEvent(): void
    {
        $event = EventFactory::make([
            'id'          => 'evt_2',
            'type'        => 'user.created',
            'createdAt'   => '2026-11-09T10:00:00.000Z',
            'tenant'      => 'tnt_1',
            'environment' => 'sandbox',
            'data'        => ['userId' => 'usr_1', 'user' => ['id' => 'usr_1', 'email' => 'jane@acme.com']],
        ], NineEvent::SOURCE_WEBHOOK);

        $this->assertInstanceOf(UserCreated::class, $event);
        $this->assertTrue($event->fromWebhook());
        $this->assertSame('jane@acme.com', $event->user()['email']);
        $this->assertSame('tnt_1', $event->tenant());
        $this->assertSame('sandbox', $event->environment());
    }

    public function testBothSessionEventsMapToOneClass(): void
    {
        $this->assertInstanceOf(SessionsRevoked::class, EventFactory::make(['type' => 'session.revoked'], 'adapter'));
        $this->assertInstanceOf(SessionsRevoked::class, EventFactory::make(['type' => 'user.sessions_revoked'], 'webhook'));
    }

    public function testSessionAndTokenRevocationWebhooks(): void
    {
        $session = EventFactory::make(['id' => 'e1', 'type' => 'session.revoked', 'data' => ['sid' => 'sid_1', 'userId' => 'usr_1']], 'webhook');
        $this->assertInstanceOf(SessionsRevoked::class, $session);
        $this->assertSame('sid_1', $session->sid());
        $this->assertSame('usr_1', $session->userId());

        $token = EventFactory::make(['id' => 'e2', 'type' => 'token.revoked', 'data' => ['type' => 'access_token', 'jti' => 'j1', 'sid' => 'sid_1', 'clientId' => 'app_1', 'userId' => 'usr_1']], 'webhook');
        $this->assertInstanceOf(TokenRevoked::class, $token);
        $this->assertTrue($token->isAccessToken());
        $this->assertSame('j1', $token->jti());
        $this->assertSame('app_1', $token->clientId());
        $this->assertSame('sid_1', $token->sid());

        $refresh = EventFactory::make(['type' => 'token.revoked', 'data' => ['type' => 'refresh_token', 'clientId' => 'app_1']], 'webhook');
        $this->assertSame('refresh_token', $refresh->tokenType());
        $this->assertNull($refresh->jti());
    }

    public function testUnknownTypesStillProduceEventReceived(): void
    {
        $this->assertNull(EventFactory::make(['type' => 'user.future_thing'], 'webhook'));
        $all = EventFactory::all(['type' => 'user.future_thing', 'data' => 'not-an-array'], 'webhook');
        $this->assertCount(1, $all);
        $this->assertInstanceOf(EventReceived::class, $all[0]);
        $this->assertSame([], $all[0]->data());
    }

    public function testAllDispatchesSpecificThenGeneric(): void
    {
        $all = EventFactory::all(['type' => 'user.updated', 'data' => ['userId' => 'u', 'fields' => ['name']]], 'adapter');
        $this->assertInstanceOf(UserUpdated::class, $all[0]);
        $this->assertInstanceOf(EventReceived::class, $all[1]);
        $this->assertSame(['name'], $all[0]->data()['fields']);
    }
}
