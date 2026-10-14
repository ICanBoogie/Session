<?php

namespace ICanBoogie\Session;

use ICanBoogie\Session;
use ICanBoogie\SessionFlash;
use ICanBoogie\SessionSegment;
use PHPUnit\Framework\TestCase;

final class FlashTest extends TestCase
{
    private string $segment_name;

    private SessionSegment|Segment $segment;

    protected function setUp(): void
    {
        $session = new Session();
        $this->segment_name = uniqid();
        $this->segment = new Segment($this->segment_name, $session);
    }

    /**
     * @return array<mixed>
     */
    private function flash_data(string $segment_name): array
    {
        $segment = $_SESSION[$segment_name] ?? [];

        assert(is_array($segment));

        $data = $segment[SessionFlash::SESSION_FLASH] ?? [];

        assert(is_array($data));

        return $data;
    }

    public function test_flash(): void
    {
        $segment = $this->segment;
        $segment_name = $this->segment_name;
        $property = uniqid();
        $value = uniqid();
        $flash = $segment->flash;

        $this->assertInstanceOf(SessionFlash::class, $flash);

        $this->assertFalse(isset($flash[$property]));
        $flash[$property] = $value;
        $this->assertTrue(isset($flash[$property]));
        $this->assertSame($value, $this->flash_data($segment_name)[$property]);
        $value_read = $segment->flash[$property];
        $this->assertSame($value, $value_read);
        $this->assertArrayNotHasKey($property, $this->flash_data($segment_name));
        $this->assertSame($value, $segment->flash[$property]);

        # unset
        $flash[$property] = $value;
        $this->assertSame($value, $this->flash_data($segment_name)[$property]);
        unset($flash[$property]);
        $this->assertArrayNotHasKey($property, $this->flash_data($segment_name));
        $this->assertNull($flash[$property]);
    }

    public function test_should_return_null_on_undefined_offset(): void
    {
        $this->assertNull($this->segment->flash[uniqid()]);
    }

    public function test_getting_a_flash_should_not_start_session(): void
    {
        $session = new NeverStartingSession();

        $this->assertInstanceOf(SessionFlash::class, $session->flash);
        $this->assertInstanceOf(SessionFlash::class, $session->segments[uniqid()]->flash);
    }
}
