<?php

namespace ICanBoogie;

use ICanBoogie\Session\CookieParams;

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

	public function test_from()
	{
		$session = Session::from();

		$this->assertInstanceOf(Session::class, $session);

		$this->setExpectedException(\LogicException::class);

		Session::from();
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

			[ 'id', '', uniqid() ],
			[ 'name', ini_get('session.name'), uniqid() ],
			[ 'cache_limiter', ini_get('session.cache_limiter'), uniqid() ],
			[ 'cache_expire', ini_get('session.cache_expire'), mt_rand(100, 1000) ],
			[ 'module_name', Session::DEFAULT_OPTION_MODULE_NAME, 'files' ],
			[ 'save_path', ini_get('session.save_path'), uniqid() ],
			[ 'cookie_params', session_get_cookie_params(), [

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
		$id = uniqid();
		$session = $this->session;
		$this->assertFalse($session->is_referenced);

		$session->id = $id;
		$_COOKIE[$session->name] = $id;
		$this->assertTrue($session->is_referenced);

		# reset
		$session->id = '';
		unset($_COOKIE[$session->name]);
	}

	public function test_segment()
	{
		$this->assertEquals(Session::DEFAULT_OPTION_SEGMENT, $this->session->segment);

		$segment = uniqid();
		$session = new Session([ Session::OPTION_SEGMENT => $segment ]);
		$this->assertEquals($segment, $session->segment);
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
}
