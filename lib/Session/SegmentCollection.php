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

use ICanBoogie\OffsetNotWritable;
use ICanBoogie\Session;
use ICanBoogie\SessionSegment;

/**
 * A collection of session segments.
 */
class SegmentCollection implements \ArrayAccess, \IteratorAggregate
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var SessionSegment[]
	 */
	private $segments = [];

	/**
	 * @param Session $session
	 */
	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	/**
	 * @inheritdoc
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->segments);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($segment_name)
	{
		return isset($this->session[$segment_name]);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet($segment_name)
	{
		$segment = &$this->segments[$segment_name];

		return $segment ?: $segment = new Segment($segment_name, $this->session);
	}

	/**
	 * @inheritdoc
	 *
	 * @throws OffsetNotWritable in attempt to write on a segment.
	 */
	public function offsetSet($segment_name, $value)
	{
		throw new OffsetNotWritable("Segment offsets are not writable (`$segment_name`)`");
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($segment_name)
	{
		unset($this->segments[$segment_name]);
		unset($this->session[$segment_name]);
	}
}
