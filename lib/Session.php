<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

use ICanBoogie\Accessor\AccessorTrait;
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
 * @property string $id Current session id.
 * @property string $name Current session name.
 * @property string $cache_limiter Current cache limiter.
 * @property string $cache_expire Current cache expire.
 * @property array $cookie_params Session cookie parameters.
 * @property string $module_name Current session module.
 * @property string $save_path Current session save path.
 * @property-read int $status Current session status.
 * @property-read bool $is_disabled Whether sessions are enabled, but none exists.
 * @property-read bool $is_active Whether sessions are enabled, and one exists.
 * @property-read bool $has_none Whether sessions are enabled, but none exists.
 * @property-read bool $is_referenced Whether session id is referenced in the cookie.
 * @property-read SegmentCollection $segments Session segments.
 * @property SessionFlash $flash The session flash.
 * @property-read string $token Current session token that can be used to prevent CSRF.
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
	/**
	 * @uses get_id
	 * @uses set_id
	 * @uses get_name
	 * @uses set_name
	 * @uses get_token
	 * @uses get_cache_limiter
	 * @uses set_cache_limiter
	 * @uses get_cache_expire
	 * @uses set_cache_expire
	 * @uses get_module_name
	 * @uses set_module_name
	 * @uses get_save_path
	 * @uses set_save_path
	 * @uses get_cookie_params
	 * @uses set_cookie_params
	 * @uses get_status
	 * @uses get_is_disabled
	 * @uses get_has_none
	 * @uses get_is_referenced
	 * @uses get_reference
	 * @uses get_segments
	 * @uses get_flash
	 */
	use AccessorTrait, SegmentTrait
	{
		SegmentTrait::__get insteadof AccessorTrait;
	}

	/**
	 * Name of the session token, may be used as form hidden input name.
	 */
	const TOKEN_NAME = '__SESSION_TOKEN__';

	/**
	 * @codeCoverageIgnore
	 */
	protected function get_id(): string
	{
		return session_id();
	}

	private function set_id(string $id): void
	{
		if (session_id() === $id) {
			return;
		}

		session_id($id);
	}

	private function get_name(): string
	{
		return session_name();
	}

	private function set_name(string $name): void
	{
		if (session_name() === $name) {
			return;
		}

		session_name($name);
	}

	/**
	 * @throws \Exception when the token cannot be generated.
	 */
	private function get_token(): string
	{
		$token = &$this[self::TOKEN_NAME];

		return $token
			?: $token = $this->generate_token();
	}

	private function get_cache_limiter(): string
	{
		return session_cache_limiter();
	}

	private function set_cache_limiter(string $cache_limiter): void
	{
		session_cache_limiter($cache_limiter);
	}

	private function get_cache_expire(): string
	{
		return session_cache_expire();
	}

	private function set_cache_expire(string $cache_expire)
	{
		session_cache_expire($cache_expire);
	}

	private function get_module_name(): string
	{
		return session_module_name();
	}

	private function set_module_name(string $module)
	{
		if (session_module_name() === $module) {
			return;
		}

		session_module_name($module);
	}

	private function get_save_path(): string
	{
		return session_save_path();
	}

	private function set_save_path(string $path): void
	{
		if (session_save_path() === $path) {
			return;
		}

		session_save_path($path);
	}

	private function get_cookie_params(): array
	{
		return session_get_cookie_params();
	}

	private function set_cookie_params(array $params): void
	{
		$lifetime = CookieParams::DEFAULT_LIFETIME;
		$path     = CookieParams::DEFAULT_PATH;
		$domain   = CookieParams::DEFAULT_DOMAIN;
		$secure   = CookieParams::DEFAULT_SECURE;
		$httponly = CookieParams::DEFAULT_HTTP_ONLY;

		extract($params, EXTR_OVERWRITE);

		session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
	}

	private function get_status(): int
	{
		return session_status();
	}

	protected function get_is_active(): bool
	{
		return $this->status === PHP_SESSION_ACTIVE;
	}

	private function get_is_disabled(): bool
	{
		return $this->status === PHP_SESSION_DISABLED;
	}

	private function get_has_none(): bool
	{
		return $this->status === PHP_SESSION_NONE;
	}

	/**
	 * Whether sessions id is referenced in the cookie.
	 *
	 * @return bool
	 */
	private function get_is_referenced()
	{
		return !empty($_COOKIE[$this->name]);
	}

	private function &get_reference(): array
	{
		$this->start_or_reuse();

		return $_SESSION;
	}

	/**
	 * @var SegmentCollection
	 */
	private $segments;

	private function get_segments(): SegmentCollection
	{
		return $this->segments
			?: $this->segments = new SegmentCollection($this);
	}

	/**
	 * @var SessionFlash
	 */
	private $flash;

	/**
	 * @inheritdoc
	 */
	private function get_flash(): SessionFlash
	{
		return $this->flash
			?: $this->flash = new Flash($this);
	}

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$normalize_options = new NormalizeOptions;

		foreach ($normalize_options($options) as $option => $value)
		{
			$this->$option = $value;
		}
	}

	/**
	 * Forward selected method to session functions.
	 *
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments)
	{
		$this->assert_is_forwardable($name);

		("session_$name")(...$arguments);
	}

	/**
	 * Assert that a method is forwardable to a session function.
	 *
	 * @param string $name
	 *
	 * @throws \BadMethodCallException if the method is not forwardable
	 */
	private function assert_is_forwardable(string $name): void
	{
		if (!in_array($name, [ 'abort', 'commit', 'decode', 'destroy', 'encode', 'regenerate_id', 'reset' ]))
		{
			$method = get_called_class() . "::$name()";

			throw new \BadMethodCallException("Unknown method: $method.");
		}
	}

	/**
	 * Initialize session data.
	 *
	 * **Note:** If PHP is running from CLI the `session_start()` method is not invoked but a fake
	 * `$_SESSION` is still created.
	 *
	 * @return bool
	 *
	 * @see session_start()
	 *
	 * @codeCoverageIgnore
	 */
	public function start(): bool
	{
		if (PHP_SAPI === 'cli')
		{
			if (isset($_SESSION))
			{
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
		if ($this->is_active)
		{
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
		if (PHP_SAPI === 'cli')
		{
			$_SESSION = [];

			return;
		}

		session_unset();
	}

	/**
	 * Update the current session id and token.
	 *
	 * @return bool `true` on success or `false` on failure.
	 *
	 * @throws \Exception when the token cannot be generated.
	 */
	public function regenerate(): bool
	{
		$this[self::TOKEN_NAME] = $this->generate_token();

		return $this->regenerate_id(true);
	}

	/**
	 * @throws \Exception
	 */
	private function generate_token(): string
	{
		return hash('sha384', random_bytes(4096));
	}

	/**
	 * Verify that a given token matches the session's token.
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	public function verify_token(string $token): bool
	{
		return $this[self::TOKEN_NAME] === $token;
	}
}
