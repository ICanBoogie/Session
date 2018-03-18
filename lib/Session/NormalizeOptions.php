<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Session;

use ICanBoogie\SessionOptions;
use function array_intersect_key;
use function array_replace_recursive;
use function session_cache_expire;
use function session_cache_limiter;
use function session_get_cookie_params;
use function session_module_name;
use function session_name;
use function session_save_path;

/**
 * Normalize session options.
 */
class NormalizeOptions
{
	/**
	 * @param array $options
	 *
	 * @return array Normalized options.
	 */
	public function __invoke(array $options): array
	{
		$default_options = static::resolve_default_options();
		$options = array_intersect_key(array_replace_recursive($default_options, $options), $default_options);

		if (empty($options[SessionOptions::OPTION_ID])) {
			unset($options[SessionOptions::OPTION_ID]);
		}

		return $options;
	}

	private function resolve_default_options(): array
	{
		return [

			SessionOptions::OPTION_NAME => session_name(),
			SessionOptions::OPTION_CACHE_LIMITER => session_cache_limiter(),
			SessionOptions::OPTION_CACHE_EXPIRE => session_cache_expire(),
			SessionOptions::OPTION_COOKIE_PARAMS => $this->resolve_default_cookie_params(),
			SessionOptions::OPTION_MODULE_NAME => session_module_name(),
			SessionOptions::OPTION_SAVE_PATH => session_save_path(),

		] + SessionOptions::DEFAULTS;
	}

	private function resolve_default_cookie_params(): array
	{
		return session_get_cookie_params() + CookieParams::DEFAULTS;
	}
}
