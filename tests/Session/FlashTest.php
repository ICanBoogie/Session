<?php

namespace ICanBoogie\Session;

use ICanBoogie\Session;
use ICanBoogie\SessionFlash;
use ICanBoogie\SessionSegment;

class FlashTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var string
	 */
	private $segment_name;

	/**
	 * @var SessionSegment
	 */
	private $segment;

	public function setUp()
	{
		$this->session = new Session;
		$this->segment_name = uniqid();
		$this->segment = new Segment($this->segment_name, $this->session);
	}

	public function test_flash()
	{
		$segment = $this->segment;
		$segment_name = $this->segment_name;
		$property = uniqid();
		$value = uniqid();

		$this->assertInstanceOf(SessionFlash::class, $segment->flash);

		$segment->flash[$property] = $value;
		$this->assertSame($value, $_SESSION[$segment_name][SessionFlash::SESSION_FLASH][$property]);
		$value_read = $segment->flash[$property];
		$this->assertSame($value, $value_read);
		$this->assertArrayNotHasKey($property, $_SESSION[$segment_name][SessionFlash::SESSION_FLASH]);
		$this->assertSame($value, $segment->flash[$property]);
	}

	public function test_should_return_null_on_undefined_offset()
	{
		$this->assertNull($this->segment->flash[uniqid()]);
	}

	public function test_getting_a_flash_should_not_start_session()
	{
		$session = $this->getMockBuilder(Session::class)
			->setMethods([ 'start', 'start_or_reuse' ])
			->disableOriginalConstructor()
			->getMock();
		$session
			->expects($this->never())
			->method('start');
		$session
			->expects($this->never())
			->method('start_or_reuse');

		/* @var $session Session */

		$this->assertInstanceOf(SessionFlash::class, $session->flash);
		$this->assertInstanceOf(SessionFlash::class, $session->segments[uniqid()]->flash);
	}
}
