<?php

namespace Nine\Laravel\Events;

/**
 * `session.revoked` (one session ended: data `sid` and the user; adapter and webhook) or
 * `user.sessions_revoked` (all of a user's sessions; webhook).
 */
class SessionsRevoked extends NineEvent
{
}
