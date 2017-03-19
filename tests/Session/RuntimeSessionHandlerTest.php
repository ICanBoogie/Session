<?php

namespace ICanBoogie\Session;

class RuntimeSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function test_handler()
	{
		$handler = new RuntimeSessionHandler();
		$session = [ uniqid() => uniqid() ];
		$session_id = uniqid();
		$this->assertTrue($handler->open($session_id, uniqid()));
		$this->assertTrue($handler->write($session_id, $session));
		$this->assertSame($session, $handler->read($session_id));
		$this->assertTrue($handler->close($session_id));
		$this->assertTrue($handler->destroy($session_id));
		$this->assertNull($handler->read($session_id));
		$this->assertTrue($handler->gc(mt_rand(10, 20)));
	}
}
