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

use ICanBoogie\SessionFlash;
use ICanBoogie\SessionSegment;

final class Flash implements SessionFlash
{
	/**
	 * @var SessionSegment
	 */
	private $segment;

	/**
	 * @var array
	 */
	private $volatile = [];

	public function __construct(SessionSegment $segment)
	{
		$this->segment = $segment;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		return isset($this->volatile[$offset]) || isset($this->get_flash_reference()[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function &offsetGet($offset)
	{
		$reference = &$this->get_flash_reference();

		if (isset($reference[$offset]))
		{
			$this->volatile[$offset] = $reference[$offset];

			unset($reference[$offset]);
		}

		return $this->volatile[$offset];
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		$this->get_flash_reference()[$offset] = $this->volatile[$offset] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		unset($this->volatile[$offset]);
		unset($this->get_flash_reference()[$offset]);
	}

	/**
	 * Return the flash reference.
	 *
	 * @return array
	 */
	private function &get_flash_reference()
	{
		$reference = &$this->segment->reference[SessionFlash::SESSION_FLASH];

		if ($reference === null)
		{
			$reference = [];
		}

		return $reference;
	}
}
