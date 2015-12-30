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

use ICanBoogie\Session;

/**
 * A session segment.
 */
class Segment implements \ArrayAccess
{
	use SegmentTrait;

	/**
	 * @var string
	 */
	private $segment_name;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @param string $segment_name
	 * @param Session $session
	 */
	public function __construct($segment_name, Session $session)
	{
		$this->segment_name = $segment_name;
		$this->session = $session;
	}

	/**
	 * Starts a new session or reuse the current one.
	 */
	protected function &get_reference()
	{
		return $this->session->reference[$this->segment_name];
	}
}
