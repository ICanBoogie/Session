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

use ICanBoogie\Session\CookieParams;
use ICanBoogie\Session\SegmentCollection;

class SessionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Session
	 */
	private $session;

	public function setUp()
	{
		$this->session = new Session;
	}

	/**
	 * @dataProvider provide_test_property
	 *
	 * @param string $property
	 * @param mixed $default
	 * @param mixed $custom
	 */
	public function test_property($property, $default, $custom)
	{
		$session = $this->session;
		$this->assertEquals($default, $session->$property);

		$session->$property = $custom;
		$this->assertEquals($custom, $session->$property);
	}

	public function provide_test_property()
	{
		return [

			[ Session::OPTION_ID, '', md5(uniqid()) ],
			[ Session::OPTION_NAME, ini_get('session.name'), 'name-' . uniqid() ],
			[ Session::OPTION_CACHE_LIMITER, ini_get('session.cache_limiter'), uniqid() ],
			[ Session::OPTION_CACHE_EXPIRE, ini_get('session.cache_expire'), mt_rand(100, 1000) ],
			[ Session::OPTION_MODULE_NAME, Session::DEFAULT_MODULE_NAME, 'files' ],
			[ Session::OPTION_SAVE_PATH, ini_get('session.save_path'), uniqid() ],
			[ Session::OPTION_COOKIE_PARAMS, session_get_cookie_params(), [

					CookieParams::OPTION_LIFETIME => mt_rand(100, 1000),
					CookieParams::OPTION_PATH => '/' . uniqid(),
					CookieParams::OPTION_DOMAIN => uniqid(),
					CookieParams::OPTION_HTTP_ONLY => false,
					CookieParams::OPTION_SECURE => true

				] + session_get_cookie_params() ],

		];
	}

	public function test_status()
	{
		$this->assertSame(PHP_SESSION_NONE, $this->session->status);
	}

	public function test_is_active()
	{
		$this->assertFalse($this->session->is_active);
	}

	public function test_is_desabled()
	{
		$this->assertEquals(!ini_get('session.use_cookies'), $this->session->is_disabled);
	}

	public function test_has_none()
	{
		$this->assertTrue($this->session->has_none);
	}

	public function test_is_referenced()
	{
		$id = 'sid-' . uniqid();
		$session = $this->session;
		$this->assertFalse($session->is_referenced);

		$session->id = $id;
		$_COOKIE[$session->name] = $id;
		$this->assertTrue($session->is_referenced);

		# reset
		$session->id = '';
		unset($_COOKIE[$session->name]);
	}

	public function test_segments()
	{
		$this->assertInstanceOf(SegmentCollection::class, $this->session->segments);
	}

	public function test_reference()
	{
		$property = uniqid();
		$v1 = uniqid();
		$v2 = uniqid();
		$this->session[$property] = $v1;
		$reference = &$this->session->reference;

		$this->assertInternalType('array', $reference);
		$this->assertSame($v1, $reference[$property]);
		$reference[$property] = $v2;
		$this->assertSame($v2, $this->session[$property]);
	}

	public function test_array_access()
	{
		$name = uniqid();
		$value = uniqid();

		$session = $this->session;

		$this->assertFalse(isset($session[$name]));
		$session[$name] = $value;
		$this->assertTrue(isset($session[$name]));
		$this->assertSame($value, $session[$name]);
		unset($session[$name]);
		$this->assertFalse(isset($session[$name]));
	}

	public function test_forward_method()
	{
		$this->session->commit();
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function test_forward_invalid_method()
	{
		$method = 'method_' . uniqid();

		$this->session->$method();
	}

	public function test_should_not_start_if_session_already_active()
	{
		$session = $this->getMockBuilder(Session::class)
			->setMethods([ 'get_is_active', 'start' ])
			->getMock();
		$session
			->expects($this->once())
			->method('get_is_active')
			->willReturn(true);
		$session
			->expects($this->never())
			->method('start');

		/* @var $session Session */

		$session->start_or_reuse();
	}

	public function test_should_start_if_no_session_is_active()
	{
		$session = $this->getMockBuilder(Session::class)
			->setMethods([ 'get_is_active', 'start' ])
			->getMock();
		$session
			->expects($this->once())
			->method('get_is_active')
			->willReturn(false);
		$session
			->expects($this->once())
			->method('start');

		/* @var $session Session */

		$session->start_or_reuse();
	}

	public function test_clear()
	{
		$session = $this->session;
		$property = uniqid();
		$value = uniqid();
		$session[$property] = $value;
		$session->clear();
		$this->assertFalse(isset($session[$property]));
	}

	public function test_token()
	{
		$session = $this->session;
		$this->assertFalse($session->verify_token(uniqid()));

		$token = $session->token;
		$this->assertNotEmpty($token);
		$this->assertSame($token, $session->token);
		$this->assertTrue($session->verify_token($token));
	}
}
