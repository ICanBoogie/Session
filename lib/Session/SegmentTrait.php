<?php

namespace ICanBoogie\Session;

/**
 * Requires the using class to implement `$reference`.
 *
 * @see \ICanBoogie\SessionSegment
 *
 * @internal
 */
trait SegmentTrait
{
    /**
     * @inheritdoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->reference[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function &offsetGet(mixed $offset): mixed
    {
        return $this->reference[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $reference = &$this->reference;
        $reference[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $reference = &$this->reference;

        unset($reference[$offset]);
    }
}
