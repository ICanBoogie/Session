<?php

namespace ICanBoogie\Session;

use ICanBoogie\Session;
use LogicException;

/**
 * A session that fails when it is started.
 */
final class NeverStartingSession extends Session
{
    #[\Override]
    public function start(): bool
    {
        throw new LogicException("The session must not be started.");
    }

    #[\Override]
    public function start_or_reuse(): void
    {
        throw new LogicException("The session must not be started or reused.");
    }
}
