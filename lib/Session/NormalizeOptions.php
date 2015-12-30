<?php

namespace ICanBoogie\Session;

use ICanBoogie\SessionOptions;

/**
 * Normalizes session options.
 */
class NormalizeOptions implements SessionOptions
{
	static private $default_options = [

		self::OPTION_ID            => self::DEFAULT_ID,
		self::OPTION_NAME          => self::DEFAULT_NAME,
		self::OPTION_CACHE_LIMITER => self::DEFAULT_CACHE_LIMITER,
		self::OPTION_CACHE_EXPIRE  => self::DEFAULT_CACHE_EXPIRE,
		self::OPTION_MODULE_NAME   => self::DEFAULT_MODULE_NAME,
		self::OPTION_SAVE_PATH     => self::DEFAULT_SAVE_PATH,

	];

	static private $default_cookie_params = [

		CookieParams::OPTION_LIFETIME  => CookieParams::DEFAULT_LIFETIME,
		CookieParams::OPTION_PATH      => CookieParams::DEFAULT_PATH,
		CookieParams::OPTION_DOMAIN    => CookieParams::DEFAULT_DOMAIN,
		CookieParams::OPTION_SECURE    => CookieParams::DEFAULT_SECURE,
		CookieParams::OPTION_HTTP_ONLY => CookieParams::DEFAULT_HTTP_ONLY,

	];

	/**
	 * @param array $options
	 *
	 * @return array Normalized options.
	 */
	public function __invoke(array $options)
	{
		$default_options = static::resolve_default_options();
		$options = array_intersect_key(array_replace_recursive($default_options, $options), $default_options);

		if (empty($options[self::OPTION_ID])) {
			unset($options[self::OPTION_ID]);
		}

		return $options;
	}

	/**
	 * Returns default options.
	 *
	 * @return array
	 */
	protected function resolve_default_options()
	{
		return [

			self::OPTION_NAME => ini_get('session.name'),
			self::OPTION_CACHE_LIMITER => ini_get('session.cache_limiter'),
			self::OPTION_CACHE_EXPIRE => ini_get('session.cache_expire'),
			self::OPTION_COOKIE_PARAMS => $this->resolve_default_cookie_params(),

		] + self::$default_options;
	}

	/**
	 * Returns default cookie params.
	 *
	 * @return array
	 */
	protected function resolve_default_cookie_params()
	{
		return session_get_cookie_params() + self::$default_cookie_params;
	}
}
