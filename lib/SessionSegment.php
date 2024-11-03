<?php

namespace ICanBoogie;

use ArrayAccess;

/**
 * An interface for session segments.
 *
 * @property array<string, mixed> $reference A reference to the session segment array.
 * @property SessionFlash $flash The session segment flash.
 *
 * @extends ArrayAccess<string, mixed>
 */
interface SessionSegment extends ArrayAccess
{
    /**
     * Clear all data from the segment.
     */
    public function clear(): void;
}
