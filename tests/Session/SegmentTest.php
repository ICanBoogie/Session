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
use ICanBoogie\SessionSegment;
use PHPUnit\Framework\TestCase;

class SegmentTest extends TestCase
{
	public function test_array_access()
	{
		$session = new Session();
		$segment_name = uniqid();
		$segment = new Segment($segment_name, $session);
		$property = uniqid();
		$value = uniqid();

		$this->assertFalse(isset($segment[$property]));

		$segment[$property] = $value;
		$this->assertTrue(isset($segment[$property]));
		$this->assertSame($value, $segment[$property]);
		$this->assertSame($value, $session[$segment_name][$property]);

		unset($segment[$property]);
		$this->assertFalse(isset($segment[$property]));
		$this->assertFalse(isset($session[$segment_name][$property]));
	}

	public function test_clear()
	{
		$session = new Session();
		$segment_name = uniqid();
		$segment = new Segment($segment_name, $session);
		$property = uniqid();
		$value = uniqid();
		$segment[$property] = $value;
		$segment->clear();
		$this->assertFalse(isset($segment[$property]));
	}

	public function test_getting_a_segment_should_not_start_session()
	{
		$session = $this->getMockBuilder(Session::class)
			->onlyMethods([ 'start', 'start_or_reuse' ])
			->disableOriginalConstructor()
			->getMock();
		$session
			->expects($this->never())
			->method('start');
		$session
			->expects($this->never())
			->method('start_or_reuse');

		/* @var $session Session */

		$this->assertInstanceOf(SegmentCollection::class, $session->segments);
		$this->assertInstanceOf(SessionSegment::class, $session->segments[uniqid()]);
	}
}
