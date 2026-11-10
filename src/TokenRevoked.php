<?php

namespace Nine\Laravel\Events;

/**
 * `token.revoked` (webhook): an access or refresh token was revoked through
 * /oauth/revoke. Access tokens carry their `jti`; hybrid token verifiers evict by it.
 */
class TokenRevoked extends NineEvent
{
    /** access_token | refresh_token */
    public function tokenType(): string
    {
        return (string) ($this->data()['token_type'] ?? '');
    }

    public function isAccessToken(): bool
    {
        return $this->tokenType() === 'access_token';
    }

    /** JWT id of a revoked access token (null for refresh tokens). */
    public function jti(): ?string
    {
        $jti = $this->data()['jti'] ?? null;
        return $jti === null ? null : (string) $jti;
    }

    public function clientId(): ?string
    {
        $id = $this->data()['client_id'] ?? null;
        return $id === null ? null : (string) $id;
    }
}
