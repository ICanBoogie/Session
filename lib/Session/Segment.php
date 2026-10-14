<?php

namespace ICanBoogie\Session;

use ICanBoogie\Session;
use ICanBoogie\SessionFlash;
use ICanBoogie\SessionSegment;

/**
 * A session segment.
 *
 * @internal
 */
final class Segment implements SessionSegment
{
    use SegmentTrait;

    /**
     * The session segment flash.
     */
    public SessionFlash $flash {
        get => $this->flash ?? $this->flash = new Flash($this);
    }

    /**
     * A reference to the segment in the session.
     *
     * @var array<string, mixed>
     */
    public array $reference {
        &get {
            /** @var array<string, mixed>|null $reference */
            $reference = &$this->session->reference[$this->segment_name];

            if ($reference === null) {
                $reference = [];
            }

            return $reference;
        }
    }

    public function __construct(
        private readonly string $segment_name,
        private readonly Session $session
    ) {
    }

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        $reference = &$this->reference;
        $reference = [];
    }
}
