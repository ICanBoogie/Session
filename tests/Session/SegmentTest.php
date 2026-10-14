<?php

namespace ICanBoogie\Session;

use ICanBoogie\Session;
use ICanBoogie\SessionSegment;
use PHPUnit\Framework\TestCase;

class SegmentTest extends TestCase
{
    public function test_array_access(): void
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
        $this->assertSame($value, $session->segments[$segment_name]->reference[$property]);

        unset($segment[$property]);
        $this->assertFalse(isset($segment[$property]));
        $this->assertArrayNotHasKey($property, $session->segments[$segment_name]->reference);
    }

    public function test_clear(): void
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

    public function test_getting_a_segment_should_not_start_session(): void
    {
        $session = new NeverStartingSession();

        $this->assertInstanceOf(SegmentCollection::class, $session->segments);
        $this->assertInstanceOf(SessionSegment::class, $session->segments[uniqid()]);
    }
}
