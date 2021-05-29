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

class SegmentCollectionTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var SegmentCollection
	 */
	private $segments;

	protected function setUp(): void
	{
		$this->session = new Session;
		$this->segments = new SegmentCollection($this->session);
	}

	public function test_array_access()
	{
		$segment_name = uniqid();
		$property = uniqid();
		$value = uniqid();
		$this->assertFalse(isset($this->segments[$segment_name]));
		$this->segments[$segment_name][$property] = $value;
		$this->assertTrue(isset($this->segments[$segment_name]));
		$this->assertSame($value, $this->segments[$segment_name][$property]);
		$this->assertSame($value, $this->session[$segment_name][$property]);

		unset($this->segments[$segment_name]);
		$this->assertFalse(isset($this->segments[$segment_name]));
	}

	public function test_iterator()
	{
		$segments = $this->segments;

		foreach ($segments as $segment_name => $segment)
		{
			$this->fail("There should be no segment");
		}

		$segment_name = uniqid();
		$property = uniqid();
		$value = uniqid();

		$this->segments[$segment_name][$property] = $value;

		foreach ($segments as $k => $segment)
		{
			$this->assertEquals($segment_name, $k);
			$this->assertInstanceOf(Segment::class, $segment);
			unset($segments[$segment_name]);

			return;
		}

		$this->fail("There should have been one segment");
	}

	public function test_offset_set_should_throw_an_exception()
	{
		$this->expectException(\ICanBoogie\OffsetNotWritable::class);
		$this->segments[uniqid()] = uniqid();
	}
}
