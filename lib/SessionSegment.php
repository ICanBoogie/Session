<?php

namespace ICanBoogie;

use ArrayAccess;

/**
 * An interface for session segments.
 *
 * @extends ArrayAccess<string, mixed>
 */
interface SessionSegment extends ArrayAccess
{
    /**
     * Clear all data from the segment.
     */
    public function clear(): void;

    /**
     * A reference to the session segment array.
     *
     * @var array<string, mixed>
     */
    public array $reference {
        &get;
    }

    /**
     * The session segment flash.
     */
    public SessionFlash $flash {
        get;
    }
}
