<?php

namespace Nine\Laravel\Events;

/**
 * `otp.requested` (adapter): 9in generated a one-time code for one of your users and
 * expects you to deliver it. 9in/laravel-server sends it with its OTP notification unless
 * you turn that off and listen to this event instead.
 */
class OtpRequested extends NineEvent
{
    public function code(): string
    {
        return (string) ($this->data()['code'] ?? '');
    }

    /** email | sms */
    public function channel(): string
    {
        return (string) ($this->data()['channel'] ?? '');
    }

    /** Email address or phone number to send the code to. */
    public function to(): string
    {
        return (string) ($this->data()['to'] ?? '');
    }

    /** login | password_reset */
    public function purpose(): string
    {
        return (string) ($this->data()['purpose'] ?? 'login');
    }

    public function expiresAt(): ?\DateTimeImmutable
    {
        $value = $this->data()['expiresAt'] ?? null;
        return $value ? new \DateTimeImmutable((string) $value) : null;
    }
}
