<?php

namespace ICanBoogie;

use ICanBoogie\Session\CookieParams;
use ICanBoogie\Session\SegmentCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    private Session $session;

    protected function setUp(): void
    {
        $this->session = new class ([ Session::OPTION_ID => sha1(uniqid()) ]) extends Session {
            private string $current_id = '';

            public string|false $id {
                get => $this->current_id;
                set(string|false $value) {
                    $this->current_id = $value === false ? '' : $value;
                }
            }

            public function regenerate_id(bool $delete_old_session = false): bool
            {
                $this->current_id = sha1(uniqid());

                return true;
            }
        };
    }

    #[DataProvider('provide_test_property')]
    public function test_property(string $property, mixed $default, mixed $custom): void
    {
        ini_set('session.use_cookies', '1'); // so that cookie params work properly

        $session = $this->session;
        $this->assertEquals($default, $session->$property);

        $session->$property = $custom;
        $this->assertEquals($custom, $session->$property);

        ini_set('session.use_cookies', '0');
    }

    /**
     * @return array<array{ string, mixed, mixed }>
     */
    public static function provide_test_property(): array
    {
        return [

            [ Session::OPTION_NAME, ini_get('session.name'), 'name-' . uniqid() ],
            [ Session::OPTION_CACHE_LIMITER, ini_get('session.cache_limiter'), uniqid() ],
            [ Session::OPTION_CACHE_EXPIRE, ini_get('session.cache_expire'), mt_rand(100, 1000) ],
//            [ Session::OPTION_MODULE_NAME, Session::DEFAULT_MODULE_NAME, 'files' ],
            [ Session::OPTION_SAVE_PATH, ini_get('session.save_path'), uniqid() ],
            [
                Session::OPTION_COOKIE_PARAMS,
                session_get_cookie_params(),
                [

                    CookieParams::OPTION_LIFETIME => mt_rand(100, 1000),
                    CookieParams::OPTION_PATH => '/' . uniqid(),
                    CookieParams::OPTION_DOMAIN => uniqid(),
                    CookieParams::OPTION_HTTP_ONLY => false,
                    CookieParams::OPTION_SECURE => true

                ] + session_get_cookie_params()
            ],

        ];
    }

    public function test_status(): void
    {
        $this->assertSame(PHP_SESSION_NONE, $this->session->status);
    }

    public function test_is_active(): void
    {
        $this->assertFalse($this->session->is_active);
    }

    public function test_is_disabled(): void
    {
        $this->assertEquals(session_status() === PHP_SESSION_DISABLED, $this->session->is_disabled);
    }

    public function test_has_none(): void
    {
        $this->assertTrue($this->session->has_none);
    }

    public function test_is_referenced(): void
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

    public function test_segments(): void
    {
        $this->assertInstanceOf(SegmentCollection::class, $this->session->segments);
    }

    public function test_reference(): void
    {
        $property = uniqid();
        $v1 = uniqid();
        $v2 = uniqid();
        $this->session[$property] = $v1;
        $reference = &$this->session->reference;

        $this->assertSame($v1, $reference[$property]);
        $reference[$property] = $v2;
        $this->assertSame($v2, $this->session[$property]);
    }

    public function test_array_access(): void
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

    public function test_forward_method(): void
    {
        $this->expectNotToPerformAssertions();

        $this->session->commit();
    }

    public function test_forward_invalid_method(): void
    {
        $method = 'method_' . uniqid();

        $this->expectException(\BadMethodCallException::class);
        $this->session->$method();
    }

    public function test_should_not_start_if_session_already_active(): void
    {
        $session = new StartProbeSession(true);
        $session->start_or_reuse();

        $this->assertSame(0, $session->start_count);
    }

    public function test_should_start_if_no_session_is_active(): void
    {
        $session = new StartProbeSession(false);
        $session->start_or_reuse();

        $this->assertSame(1, $session->start_count);
    }

    public function test_clear(): void
    {
        $session = $this->session;
        $property = uniqid();
        $value = uniqid();
        $session[$property] = $value;
        $session->clear();
        $this->assertFalse(isset($session[$property]));
    }

    public function test_token(): void
    {
        $session = $this->session;
        $this->assertFalse($session->verify_token(uniqid()));

        $token = $session->token;
        $this->assertNotEmpty($token);
        $this->assertSame($token, $session->token);
        $this->assertTrue($session->verify_token($token));
    }

    public function test_should_regenerate_id_and_token(): void
    {
        $session = $this->session;
        $id = $session->id;
        $token = $session->token;

        $session->regenerate();
        $this->assertNotEquals($id, $session->id);
        $this->assertNotEquals($token, $session->token);
    }
}
