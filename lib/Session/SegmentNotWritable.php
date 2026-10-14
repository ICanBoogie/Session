<?php

namespace ICanBoogie\Session;

use LogicException;

/**
 * Exception thrown in attempt to write on a segment of the segment collection.
 */
final class SegmentNotWritable extends LogicException
{
    public function __construct(string $segment_name)
    {
        parent::__construct("Segment offsets are not writable (`$segment_name`).");
    }
}
