<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

/**
 * An interface for session segments.
 *
 * @property array $reference A reference to the session segment array.
 * @property SessionFlash $flash The session segment flash.
 */
interface SessionSegment extends \ArrayAccess
{
	/**
	 * Clear all data from the segment.
	 */
	public function clear();
}
