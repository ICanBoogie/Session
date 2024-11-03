<?php

namespace ICanBoogie\Session;

use ArrayAccess;
use ArrayIterator;
use ICanBoogie\OffsetNotWritable;
use ICanBoogie\Session;
use ICanBoogie\SessionSegment;
use IteratorAggregate;
use Traversable;

/**
 * A collection of session segments.
 *
 * @implements ArrayAccess<string, SessionSegment>
 * @implements IteratorAggregate<string, SessionSegment>
 */
final class SegmentCollection implements ArrayAccess, IteratorAggregate
{
    /**
     * @var array<string, SessionSegment>
     */
    private array $segments = [];

    public function __construct(
        private readonly Session $session
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->segments);
    }

    /**
     * @inheritdoc
     *
     * @param mixed $offset Segment name.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->session[$offset]);
    }

    /**
     * @inheritdoc
     *
     * @param string $offset Segment name.
     */
    public function offsetGet(mixed $offset): SessionSegment
    {
        assert(is_string($offset));

        return $this->segments[$offset]
            ??= new Segment($offset, $this->session);
    }

    /**
     * @inheritdoc
     *
     * @param string $offset Segment name.
     *
     * @throws OffsetNotWritable in attempt to write on a segment.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        assert(is_string($offset));

        throw new OffsetNotWritable("Segment offsets are not writable (`$offset`)`");
    }

    /**
     * @inheritdoc
     *
     * @param string $offset Segment name.
     */
    public function offsetUnset(mixed $offset): void
    {
        assert(is_string($offset));

        unset($this->segments[$offset]);
        unset($this->session[$offset]); // @phpstan-ignore-line
    }
}
