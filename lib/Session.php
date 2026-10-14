<?php

namespace ICanBoogie;

use Exception;
use ICanBoogie\Session\CookieParams;
use ICanBoogie\Session\Flash;
use ICanBoogie\Session\SegmentCollection;
use ICanBoogie\Session\SegmentTrait;
use InvalidArgumentException;

use function random_bytes;
use function session_id;

use const PHP_SAPI;

/**
 * Session.
 */
class Session implements SessionSegment
{
    use SegmentTrait;

    /**
     * Name of the session token, may be used as form hidden input name.
     */
    public const string TOKEN_NAME = '__SESSION_TOKEN__';

    /**
     * Current session id.
     */
    public string|false $id {
        get => session_id();
        set {
            if ($value === false) {
                throw new InvalidArgumentException("The session id cannot be `false`.");
            }

            if (session_id() === $value) {
                return;
            }

            session_id($value);
        }
    }

    /**
     * Current session name.
     */
    public string|false $name {
        get => session_name();
        set {
            if ($value === false) {
                throw new InvalidArgumentException("The session name cannot be `false`.");
            }

            if (session_name() === $value) {
                return;
            }

            session_name($value);
        }
    }

    /**
     * Current session token that can be used to prevent CSRF.
     *
     * @throws Exception when the token cannot be generated.
     */
    public string $token {
        /**
         * @throws Exception
         */
        get {
            /** @var string|null $token */
            $token = &$this[self::TOKEN_NAME];

            return $token
                ?: $token = $this->generate_token();
        }
    }

    /**
     * Current cache limiter.
     */
    public string|false $cache_limiter {
        get => session_cache_limiter();
        set {
            if ($value === false) {
                throw new InvalidArgumentException("The cache limiter cannot be `false`.");
            }

            session_cache_limiter($value);
        }
    }

    /**
     * Current cache expire.
     */
    public int|false $cache_expire {
        get => session_cache_expire();
        set {
            if ($value === false) {
                throw new InvalidArgumentException("The cache expire cannot be `false`.");
            }

            session_cache_expire($value);
        }
    }

    /**
     * Current session module.
     */
    public string|false $module_name {
        get => session_module_name();
        set {
            if ($value === false) {
                throw new InvalidArgumentException("The session module cannot be `false`.");
            }

            if (session_module_name() === $value) {
                return;
            }

            session_module_name($value);
        }
    }

    /**
     * Current session save path.
     */
    public string|false $save_path {
        get => session_save_path();
        set {
            if ($value === false) {
                throw new InvalidArgumentException("The session save path cannot be `false`.");
            }

            if (session_save_path() === $value) {
                return;
            }

            session_save_path($value);
        }
    }

    /**
     * Session cookie parameters.
     */
    public CookieParams $cookie_params {
        get => CookieParams::from_array(session_get_cookie_params());
        set {
            $is_enabled = filter_var(ini_get('session.use_cookies'), FILTER_VALIDATE_BOOLEAN);

            if (!$is_enabled) {
                return;
            }

            // @phpstan-ignore-next-line argument.type (an empty `samesite` is valid)
            session_set_cookie_params([
                'lifetime' => $value->lifetime,
                'path' => $value->path,
                'domain' => $value->domain,
                'secure' => $value->secure,
                'httponly' => $value->http_only,
                'samesite' => $value->same_site,
            ]);
        }
    }

    /**
     * Current session status.
     */
    public int $status {
        get => session_status();
    }

    /**
     * Whether sessions is active.
     */
    public bool $is_active {
        get => $this->status === PHP_SESSION_ACTIVE;
    }

    /**
     * Whether sessions are disabled.
     */
    public bool $is_disabled {
        get => $this->status === PHP_SESSION_DISABLED;
    }

    /**
     * Whether sessions are enabled, but none exists.
     */
    public bool $has_none {
        get => $this->status === PHP_SESSION_NONE;
    }

    /**
     * Whether the session id is referenced in the cookie.
     */
    public bool $is_referenced {
        get => !empty($_COOKIE[$this->name]);
    }

    /**
     * @var array<string, mixed>
     */
    public array $reference {
        &get {
            $this->start_or_reuse();

            /** @var array<string, mixed> $session */
            $session = &$_SESSION;

            return $session;
        }
    }

    /**
     * Session segments.
     */
    public SegmentCollection $segments {
        get => $this->segments ??= new SegmentCollection($this);
    }

    /**
     * The session flash.
     */
    public SessionFlash $flash {
        get => $this->flash ??= new Flash($this);
    }

    public function __construct(SessionOptions $options = new SessionOptions())
    {
        if ($options->id !== null && $options->id !== '') {
            $this->id = $options->id;
        }

        if ($options->name !== null) {
            $this->name = $options->name;
        }

        if ($options->cache_limiter !== null) {
            $this->cache_limiter = $options->cache_limiter;
        }

        if ($options->cache_expire !== null) {
            $this->cache_expire = $options->cache_expire;
        }

        if ($options->module_name !== null) {
            $this->module_name = $options->module_name;
        }

        if ($options->save_path !== null) {
            $this->save_path = $options->save_path;
        }

        if ($options->cookie_params !== null) {
            $this->cookie_params = $options->cookie_params;
        }
    }

    /**
     * Discard session array changes and finish session.
     *
     * @see session_abort()
     */
    public function abort(): bool
    {
        return session_abort();
    }

    /**
     * Write session data and end session.
     *
     * @see session_write_close()
     */
    public function commit(): bool
    {
        return session_write_close();
    }

    /**
     * Decode session data from a session encoded string.
     *
     * @see session_decode()
     */
    public function decode(string $data): bool
    {
        return session_decode($data);
    }

    /**
     * Destroy all data registered to a session.
     *
     * @see session_destroy()
     */
    public function destroy(): bool
    {
        return session_destroy();
    }

    /**
     * Encode the current session data as a session encoded string.
     *
     * @see session_encode()
     */
    public function encode(): string|false
    {
        return session_encode();
    }

    /**
     * Update the current session id with a newly generated one.
     *
     * @see session_regenerate_id()
     */
    public function regenerate_id(bool $delete_old_session = false): bool
    {
        return session_regenerate_id($delete_old_session);
    }

    /**
     * Re-initialize session array with original values.
     *
     * @see session_reset()
     */
    public function reset(): bool
    {
        return session_reset();
    }

    /**
     * Initialize session data.
     *
     * **Note**: If PHP is running from CLI the `session_start()` method is not invoked, but a fake
     * `$_SESSION` is still created.
     *
     * @see session_start()
     *
     * @codeCoverageIgnore
     */
    public function start(): bool
    {
        if (PHP_SAPI === 'cli') {
            if (isset($_SESSION)) {
                return false;
            }

            $_SESSION = [];

            return true;
        }

        $started = session_start();
        $this->regenerate_id();

        return $started;
    }

    /**
     * Start a new session or reuse the current one.
     */
    public function start_or_reuse(): void
    {
        if ($this->is_active) {
            return;
        }

        $this->start();
    }

    /**
     * Clear the session of all data.
     *
     * @see session_unset()
     *
     * @codeCoverageIgnore
     */
    public function clear(): void
    {
        if (PHP_SAPI === 'cli') {
            $_SESSION = [];

            return;
        }

        session_unset();
    }

    /**
     * Update the current session id and token.
     *
     * @throws Exception when the token cannot be generated.
     */
    public function regenerate(): bool
    {
        $this[self::TOKEN_NAME] = $this->generate_token();

        return $this->regenerate_id(true);
    }

    /**
     * @throws Exception
     */
    private function generate_token(): string
    {
        return hash('sha384', random_bytes(4096));
    }

    /**
     * Verify that a given token matches the session's token.
     */
    public function verify_token(string $token): bool
    {
        return $this[self::TOKEN_NAME] === $token;
    }
}
