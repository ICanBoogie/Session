<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
 */
final class SegmentCollection implements ArrayAccess, IteratorAggregate
{

	/**
	 * @var SessionSegment[]
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
	 * @param mixed $offset Segment name.
	 */
	public function offsetGet(mixed $offset): SessionSegment
	{
		$segment = &$this->segments[$offset];

		return $segment ?? $segment = new Segment($offset, $this->session);
	}

	/**
	 * @inheritdoc
	 *
	 * @param mixed $offset Segment name.
	 *
	 * @throws OffsetNotWritable in attempt to write on a segment.
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new OffsetNotWritable("Segment offsets are not writable (`$offset`)`");
	}

	/**
	 * @inheritdoc
	 *
	 * @param mixed $offset Segment name.
	 */
	public function offsetUnset(mixed $offset): void
	{
		unset($this->segments[$offset]);
		unset($this->session[$offset]);
	}
}
