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
	 * @var array
	 */
	private array $volatile = [];

	public function __construct(
		private readonly SessionSegment $segment
	) {
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists(mixed $offset): bool
	{
		return isset($this->volatile[$offset]) || isset($this->get_flash_reference()[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function &offsetGet(mixed $offset): mixed
	{
		$reference = &$this->get_flash_reference();

		if (isset($reference[$offset])) {
			$this->volatile[$offset] = $reference[$offset];

			unset($reference[$offset]);
		}

		return $this->volatile[$offset];
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->get_flash_reference()[$offset] = $this->volatile[$offset] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset(mixed $offset): void
	{
		unset($this->volatile[$offset]);
		unset($this->get_flash_reference()[$offset]);
	}

	/**
	 * Return the flash reference.
	 */
	private function &get_flash_reference(): array
	{
		$reference = &$this->segment->reference[SessionFlash::SESSION_FLASH];

		if ($reference === null) {
			$reference = [];
		}

		return $reference;
	}
}
