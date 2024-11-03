<?php

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
//        $this->assertTrue($handler->gc(mt_rand(10, 20)));
    }
}
