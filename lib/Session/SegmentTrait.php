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

trait SegmentTrait
{
	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		return isset($this->get_reference()[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function &offsetGet($offset)
	{
		return $this->get_reference()[$offset];
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		$this->get_reference()[$offset] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		unset($this->get_reference()[$offset]);
	}

	/**
	 * Return the segment reference.
	 *
	 * @return array
	 */
	abstract protected function &get_reference();
}
