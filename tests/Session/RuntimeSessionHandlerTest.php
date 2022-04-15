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

use PHPUnit\Framework\TestCase;

class RuntimeSessionHandlerTest extends TestCase
{
	public function test_handler()
	{
		$handler = new RuntimeSessionHandler();
		$data = uniqid();
		$session_id = uniqid();
		$this->assertTrue($handler->open($session_id, uniqid()));
		$this->assertTrue($handler->write($session_id, $data));
		$this->assertSame($data, $handler->read($session_id));
		$this->assertTrue($handler->close());
		$this->assertTrue($handler->destroy($session_id));
		$this->assertFalse($handler->read($session_id));
//		$this->assertTrue($handler->gc(mt_rand(10, 20)));
	}
}
