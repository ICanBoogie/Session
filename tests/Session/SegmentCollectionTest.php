<?php

namespace ICanBoogie\Session;

use ICanBoogie\Session;
use PHPUnit\Framework\TestCase;

class SegmentCollectionTest extends TestCase
{
    private Session $session;
    private SegmentCollection $segments;

    protected function setUp(): void
    {
        $this->session = new Session();
        $this->segments = new SegmentCollection($this->session);
    }

    public function test_array_access(): void
    {
        $segment_name = uniqid();
        $property = uniqid();
        $value = uniqid();
        $segments = $this->segments;

        $this->assertFalse($segments->offsetExists($segment_name));
        $segment = $segments[$segment_name];
        $segment[$property] = $value;
        $this->assertTrue($segments->offsetExists($segment_name));
        $this->assertSame($value, $segment->reference[$property]);
        $this->assertSame([ $property => $value ], $this->session->reference[$segment_name]);

        unset($segments[$segment_name]);
        $this->assertFalse($segments->offsetExists($segment_name));
    }

    public function test_iterator(): void
    {
        $segments = $this->segments;

        foreach ($segments as $segment_name => $segment) {
            $this->fail("There should be no segment");
        }

        $segment_name = uniqid();
        $property = uniqid();
        $value = uniqid();

        $this->segments[$segment_name][$property] = $value;

        foreach ($segments as $k => $segment) {
            $this->assertEquals($segment_name, $k);
            $this->assertInstanceOf(Segment::class, $segment);
            unset($segments[$segment_name]);

            return;
        }

        $this->fail("There should have been one segment");
    }

    public function test_offset_set_should_throw_an_exception(): void
    {
        $this->expectException(SegmentNotWritable::class);
        $this->segments->offsetSet(uniqid(), new Segment(uniqid(), $this->session));
    }
}
