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

/**
 * @property array $reference A reference to the session array.
 * @property SessionFlash $flash The session segment flash.
 *
 * @see \ICanBoogie\SessionSegment
 */
trait SegmentTrait
{
	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset): bool
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
	 * Return a property value.
	 *
	 * **Note:** We override the method as to be able to return {@link $reference} as a reference
	 * and not a value.
	 *
	 * @return mixed
	 */
	public function &__get(string $name)
	{
		if ($name === 'reference')
		{
			return $this->get_reference();
		}

		$result = $this->accessor_get($name);

		return $result;
	}

	/**
	 * Return the segment reference.
	 *
	 * @codeCoverageIgnore
	 */
	private function &get_reference(): array
	{
		throw new \BadMethodCallException(__FUNCTION__ . " should be implemented.");
	}

	/**
	 * Return a session flash.
	 *
	 * @codeCoverageIgnore
	 */
	private function get_flash(): SessionFlash
	{
		throw new \BadMethodCallException(__FUNCTION__ . " should be implemented.");
	}
}
