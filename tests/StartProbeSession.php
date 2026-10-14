<?php

namespace ICanBoogie;

/**
 * A session that counts the calls to {@see start()} instead of starting for real.
 */
final class StartProbeSession extends Session
{
    public int $start_count = 0;

    public function __construct(private readonly bool $probe_active)
    {
        parent::__construct();
    }

    public bool $is_active {
        get => $this->probe_active;
    }

    #[\Override]
    public function start(): bool
    {
        $this->start_count++;

        return true;
    }
}
