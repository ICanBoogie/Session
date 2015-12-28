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
use ICanBoogie\Session\SessionOptions;

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
 * @property-read string $segment Default session segment.
 *
 * @property string $remote_agent_hash The remote user agent hash of the request that created the
 * session.
 * @property string $token A token that can be used to prevent cross-site request forgeries.
 */
class Session implements SessionOptions, \ArrayAccess
{
	use AccessorTrait;

	static private $default_options = [

		self::OPTION_ID            => self::DEFAULT_OPTION_ID,
		self::OPTION_NAME          => self::DEFAULT_OPTION_NAME,
		self::OPTION_CACHE_LIMITER => self::DEFAULT_OPTION_CACHE_LIMITER,
		self::OPTION_CACHE_EXPIRE  => self::DEFAULT_OPTION_CACHE_EXPIRE,
		self::OPTION_MODULE_NAME   => self::DEFAULT_OPTION_MODULE_NAME,
		self::OPTION_SAVE_PATH     => self::DEFAULT_OPTION_SAVE_PATH,
		self::OPTION_SEGMENT       => self::DEFAULT_OPTION_SEGMENT,

	];

	static private $default_cookie_params = [

		CookieParams::OPTION_LIFETIME  => CookieParams::DEFAULT_OPTION_LIFETIME,
		CookieParams::OPTION_PATH      => CookieParams::DEFAULT_OPTION_PATH,
		CookieParams::OPTION_DOMAIN    => CookieParams::DEFAULT_OPTION_DOMAIN,
		CookieParams::OPTION_SECURE    => CookieParams::DEFAULT_OPTION_SECURE,
		CookieParams::OPTION_HTTP_ONLY => CookieParams::DEFAULT_OPTION_HTTP_ONLY,

	];

	static private $instance;

	/**
	 * @param array $options
	 *
	 * @return static
	 */
	static public function from(array $options = [])
	{
		self::assert_not_instantiated();

		return self::$instance = new static($options);
	}

	/**
	 * @throws \LogicException if the session is already instantiated.
	 */
	static public function assert_not_instantiated()
	{
		if (self::$instance)
		{
			throw new \LogicException("Session already instantiated.");
		}
	}

	/**
	 * Returns default options.
	 *
	 * @return array
	 */
	static public function resolve_default_options()
	{
		return [

			self::OPTION_NAME => ini_get('session.name'),
			self::OPTION_CACHE_LIMITER => ini_get('session.cache_limiter'),
			self::OPTION_CACHE_EXPIRE => ini_get('session.cache_expire'),
			self::OPTION_COOKIE_PARAMS => static::resolve_default_cookie_params(),

		] + self::$default_options;
	}

	/**
	 * Returns default cookie params.
	 *
	 * @return array
	 */
	static public function resolve_default_cookie_params()
	{
		return session_get_cookie_params() + self::$default_cookie_params;
	}

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
		$lifetime = CookieParams::DEFAULT_OPTION_LIFETIME;
		$path     = CookieParams::DEFAULT_OPTION_PATH;
		$domain   = CookieParams::DEFAULT_OPTION_DOMAIN;
		$secure   = CookieParams::DEFAULT_OPTION_SECURE;
		$httponly = CookieParams::DEFAULT_OPTION_HTTP_ONLY;

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
	private $segment;

	/**
	 * @return string
	 */
	protected function get_segment()
	{
		return $this->segment;
	}

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		foreach ($this->normalize_options($options) as $option => $value)
		{
			$this->$option = $value;
		}
	}

	/**
	 * Normalizes options.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	protected function normalize_options(array $options)
	{
		$default_options = static::resolve_default_options();
		$options = array_intersect_key(array_replace_recursive($default_options, $options), $default_options);

		if (empty($options[self::OPTION_ID])) {
			unset($options[self::OPTION_ID]);
		}

		return $options;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		$this->ensure_started();

		return isset($_SESSION[$this->segment][$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function &offsetGet($offset)
	{
		$this->ensure_started();

		return $_SESSION[$this->segment][$offset];
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		$this->ensure_started();

		$_SESSION[$this->segment][$offset] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		$this->ensure_started();

		unset($_SESSION[$this->segment][$offset]);
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
	 * Update the current session id with a newly generated one.
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
	 * Encodes the current session data as a string.
	 *
	 * @see session_encode()
	 *
	 * @codeCoverageIgnore
	 */
	public function encode()
	{
		session_encode();
	}

	/**
	 * Decodes session data from a string.
	 *
	 * @param string $data
	 *
	 * @return bool
	 *
	 * @see session_decode()
	 *
	 * @codeCoverageIgnore
	 */
	public function decode($data)
	{
		return session_decode($data);
	}

	/**
	 * Destroys all data registered to a session.
	 *
	 * @return bool
	 *
	 * @see session_destory()
	 *
	 * @codeCoverageIgnore
	 */
	public function destroy()
	{
		return session_destroy();
	}

	/**
	 * Free all session variables.
	 *
	 * @see session_unset()
	 *
	 * @codeCoverageIgnore
	 */
	public function clear()
	{
		session_unset();
	}

	/**
	 * @see session_commit()
	 *
	 * @codeCoverageIgnore
	 */
	public function commit()
	{
		session_commit();
	}

	/**
	 * @return bool
	 *
	 * @see session_abort()
	 *
	 * @codeCoverageIgnore
	 */
	public function abort()
	{
		return session_abort();
	}

	/**
	 * @return bool
	 *
	 * @see session_reset()
	 *
	 * @codeCoverageIgnore
	 */
	public function reset()
	{
		return session_reset();
	}

	/**
	 * Ensures that the session is started.
	 *
	 * @codeCoverageIgnore
	 */
	protected function ensure_started()
	{
		if ($this->status === PHP_SESSION_ACTIVE)
		{
			return;
		}

		$this->start();
	}
}
