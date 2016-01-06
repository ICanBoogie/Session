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

use ICanBoogie\Accessor\AccessorTrait;
use ICanBoogie\Session;
use ICanBoogie\SessionFlash;
use ICanBoogie\SessionSegment;

/**
 * A session segment.
 *
 * @property array $reference A reference to the segment in the session.
 * @property SessionFlash $flash The session segment flash.
 */
class Segment implements SessionSegment
{
	use AccessorTrait, SegmentTrait
	{
		SegmentTrait::__get insteadof AccessorTrait;
	}

	/**
	 * @var string
	 */
	private $segment_name;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var SessionFlash
	 */
	private $flash;

	/**
	 * @return SessionFlash
	 */
	protected function get_flash()
	{
		return $this->flash ?: $this->flash = new Flash($this);
	}

	/**
	 * @inheritdoc
	 */
	protected function &get_reference()
	{
		return $this->session->reference[$this->segment_name];
	}

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
	 * @inheritdoc
	 */
	public function clear()
	{
		$reference = &$this->get_reference();
		$reference = [];
	}
}
