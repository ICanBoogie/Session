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
final class Segment implements SessionSegment
{
	/**
	 * @uses get_reference
	 * @uses get_flash
	 */
	use AccessorTrait;
	use SegmentTrait {
		SegmentTrait::__get insteadof AccessorTrait;
	}

	private ?SessionFlash $flash = null;

	private function get_flash(): SessionFlash
	{
		return $this->flash ?? $this->flash = new Flash($this);
	}

	private function &get_reference(): array
	{
		$reference = &$this->session->reference[$this->segment_name];

		if ($reference === null) {
			$reference = [];
		}

		return $reference;
	}

	public function __construct(
		private readonly string $segment_name,
		private readonly Session $session
	) {
	}

	/**
	 * @inheritdoc
	 */
	public function clear(): void
	{
		$reference = &$this->get_reference();
		$reference = [];
	}
}
