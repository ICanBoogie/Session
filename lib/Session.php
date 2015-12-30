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
use ICanBoogie\Session\NormalizeOptions;
use ICanBoogie\Session\SegmentCollection;
use ICanBoogie\Session\SegmentTrait;

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
 * @property-read string $segment_name Default session segment.
 * @property-read SegmentCollection $segments Session segments.
 * @property-read array $reference A reference to the session array.
 * @property-read string $token Current session token that can be used to prevent CSRF.
 *
 * @property string $remote_agent_hash The remote user agent hash of the request that created the
 * session.
 * @property string $token A token that can be used to prevent cross-site request forgeries.
 *
 * @method void abort() Discard session array changes and finish session.
 * @method void commit() Write session data and end session.
 * @method bool decode(string $data) Decodes session data from a session encoded string.
 * @method void destroy() Destroys all data registered to a session.
 * @method string encode() Encodes the current session data as a session encoded string.
 * @method void reset() Re-initialize session array with original values.
 */
class Session implements SessionOptions, \ArrayAccess
{
	use AccessorTrait, SegmentTrait;

	const TOKEN_NAME = 'session_token';

	/**
	 * @return string
	 */
	protected function get_id()
	{
		return session_id();
	}

	/**
	 * @param string $id
	 */
	protected function set_id($id)
	{
		session_id($id);
	}

	/**
	 * @return string
	 */
	protected function get_name()
	{
		return session_name();
	}

	/**
	 * @param string $name
	 */
	protected function set_name($name)
	{
		session_name($name);
	}

	/**
	 * @return string
	 */
	protected function get_token()
	{
		$token = &$this[self::TOKEN_NAME];

		if (!$token)
		{
			$token = $this->generate_token();
		}

		return $token;
	}

	/**
	 * @return string
	 */
	protected function get_cache_limiter()
	{
		return session_cache_limiter();
	}

	/**
	 * @param string $cache_limiter
	 */
	protected function set_cache_limiter($cache_limiter)
	{
		session_cache_limiter($cache_limiter);
	}

	/**
	 * @return string
	 */
	protected function get_cache_expire()
	{
		return session_cache_expire();
	}

	/**
	 * @param string $cache_expire
	 */
	protected function set_cache_expire($cache_expire)
	{
		session_cache_expire($cache_expire);
	}

	/**
	 * @return string
	 */
	protected function get_module_name()
	{
		return session_module_name();
	}

	/**
	 * @param string $module
	 */
	protected function set_module_name($module)
	{
		session_module_name($module);
	}

	/**
	 * @return string
	 */
	protected function get_save_path()
	{
		return session_save_path();
	}

	/**
	 * @param string $path
	 */
	protected function set_save_path($path)
	{
		session_save_path($path);
	}

	/**
	 * @return array
	 */
	protected function get_cookie_params()
	{
		return session_get_cookie_params();
	}

	/**
	 * @param array $params
	 */
	protected function set_cookie_params(array $params)
	{
		$lifetime = CookieParams::DEFAULT_LIFETIME;
		$path     = CookieParams::DEFAULT_PATH;
		$domain   = CookieParams::DEFAULT_DOMAIN;
		$secure   = CookieParams::DEFAULT_SECURE;
		$httponly = CookieParams::DEFAULT_HTTP_ONLY;

		extract($params, EXTR_OVERWRITE);

		session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
	}

	/**
	 * Returns the current session status.
	 *
	 * @return int
	 */
	protected function get_status()
	{
		return session_status();
	}

	/**
	 * Whether sessions are enabled, and one exists.
	 *
	 * @return bool
	 */
	protected function get_is_active()
	{
		return $this->status === PHP_SESSION_ACTIVE;
	}

	/**
	 * Whether sessions are disabled.
	 *
	 * @return bool
	 */
	protected function get_is_disabled()
	{
		return $this->status === PHP_SESSION_DISABLED;
	}

	/**
	 * Whether sessions are enabled, but none exists.
	 *
	 * @return bool
	 */
	protected function get_has_none()
	{
		return $this->status === PHP_SESSION_NONE;
	}

	/**
	 * Whether sessions id is referenced in the cookie.
	 *
	 * @return bool
	 */
	protected function get_is_referenced()
	{
		return !empty($_COOKIE[$this->name]);
	}

	/**
	 * Default segment name.
	 *
	 * @var string
	 */
	private $segment_name;

	/**
	 * @return string
	 */
	protected function get_segment_name()
	{
		return $this->segment_name;
	}

	/**
	 * @return array
	 */
	protected function &get_reference()
	{
		$this->start_or_reuse();

		return $_SESSION[$this->segment_name];
	}

	/**
	 * @var SegmentCollection
	 */
	private $segments;

	protected function get_segments()
	{
		return $this->segments;
	}

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$this->segments = new SegmentCollection($this);
		$normalize_options = new NormalizeOptions;

		foreach ($normalize_options($options) as $option => $value)
		{
			$this->$option = $value;
		}
	}

	/**
	 * Returns a property value.
	 *
	 * **Note:** We override the method as to be able to return {@link $reference} as a reference
	 * and not a value.
	 *
	 * @param string $name Property name.
	 *
	 * @return mixed
	 */
	public function &__get($name)
	{
		if ($name === 'reference')
		{
			return $this->get_reference();
		}

		$result = $this->accessor_get($name);

		return $result;
	}

	/**
	 * Forwards selected method to session functions.
	 *
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, array $arguments)
	{
		$this->assert_is_forwardable($name);

		call_user_func_array("session_$name", $arguments);
	}

	/**
	 * Asserts that a method is forwardable to a session function.
	 *
	 * @param string $name
	 *
	 * @throws \BadMethodCallException if the method is not forwardable
	 */
	protected function assert_is_forwardable($name)
	{
		if (!in_array($name, [ 'abort', 'commit', 'decode', 'destroy', 'encode', 'reset' ]))
		{
			throw new \BadMethodCallException("Unknown method: $name.");
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
	public function start()
	{
		if (PHP_SAPI === 'cli')
		{
			$_SESSION = &$_SESSION;

			return;
		}

		session_start();
		$this->regenerate_id();
	}

	/**
	 * Starts a new session or reuse the current one.
	 */
	public function start_or_reuse()
	{
		if ($this->is_active)
		{
			return;
		}

		$this->start();
	}

	/**
	 * Updates the current session id with a newly generated one.
	 *
	 * @param bool $delete_old_session
	 *
	 * @return bool|null `true` when the id is regenerated, `false` when it is not, `null` when
	 * the application is running from CLI.
	 *
	 * @see session_regenerate_id()
	 *
	 * @codeCoverageIgnore
	 */
	public function regenerate_id($delete_old_session = false)
	{
		if (PHP_SAPI === 'cli')
		{
			return null;
		}

		return session_regenerate_id($delete_old_session);
	}

	/**
	 * Generates a session token.
	 *
	 * @return string
	 */
	protected function generate_token()
	{
		return sha1(openssl_random_pseudo_bytes(1024));
	}

	/**
	 * Verifies that a given token matches the session's token.
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	public function verify_token($token)
	{
		return $this[self::TOKEN_NAME] === $token;
	}
}
