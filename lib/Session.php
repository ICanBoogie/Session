<?php

namespace ICanBoogie;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use ICanBoogie\Session\CookieParams;
use ICanBoogie\Session\Flash;
use ICanBoogie\Session\NormalizeOptions;
use ICanBoogie\Session\SegmentCollection;
use ICanBoogie\Session\SegmentTrait;

use function random_bytes;
use function session_id;

use const PHP_SAPI;

/**
 * Session.
 *
 * @method void abort() Discard session array changes and finish session.
 * @method void commit() Write session data and end session.
 * @method bool decode(string $data) Decodes session data from a session encoded string.
 * @method void destroy() Destroys all data registered to a session.
 * @method string encode() Encodes the current session data as a session encoded string.
 * @method bool regenerate_id($delete_old_session = false)  Update the current session id with a newly generated one.
 * @method void reset() Re-initialize session array with original values.
 */
class Session implements SessionOptions, SessionSegment
{
    use SegmentTrait;

    /**
     * Name of the session token, may be used as form hidden input name.
     */
    public const TOKEN_NAME = '__SESSION_TOKEN__';

    /**
     * Current session id.
     */
    public string|false $id {
        get => session_id();
        set(string|false $value) {
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
        set(string|false $value) {
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
        set(string|false $value) {
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
        set(int|false $value) {
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
        set(string|false $value) {
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
        set(string|false $value) {
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

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $normalize_options = new NormalizeOptions();

        foreach ($normalize_options($options) as $option => $value) {
            $this->$option = $value;
        }
    }

    /**
     * Forward the selected method to session functions.
     */
    public function __call(string $name, array $arguments) // @phpstan-ignore-line
    {
        $this->can_forward($name);

        // @phpstan-ignore-next-line
        ("session_$name")(...$arguments);
    }

    /**
     * Assert that a method can be forwarded to a session function.
     *
     * @param string $name
     *
     * @throws BadMethodCallException if the method cannot be forwarded
     */
    private function can_forward(string $name): void
    {
        if (!in_array($name, [ 'abort', 'commit', 'decode', 'destroy', 'encode', 'regenerate_id', 'reset' ])) {
            $method = get_called_class() . "::$name()";

            throw new BadMethodCallException("Unknown method: $method.");
        }
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
