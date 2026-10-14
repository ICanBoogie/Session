<?php

namespace ICanBoogie\Session;

use ArrayAccess;
use ArrayIterator;
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
        return $this->segments[$offset]
            ??= new Segment($offset, $this->session);
    }

    /**
     * @inheritdoc
     *
     * @param string $offset Segment name.
     *
     * @throws SegmentNotWritable in attempt to write on a segment.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new SegmentNotWritable($offset);
    }

    /**
     * @inheritdoc
     *
     * @param string $offset Segment name.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->segments[$offset]);
        unset($this->session[$offset]);
    }
}
