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

/**
 * Cookie params.
 */
interface CookieParams
{
	const OPTION_LIFETIME = 'lifetime';
	const OPTION_PATH = 'path';
	const OPTION_DOMAIN = 'domain';
	const OPTION_SECURE = 'secure';
	const OPTION_HTTP_ONLY = 'httponly';

	const DEFAULT_LIFETIME = 0;
	const DEFAULT_PATH = null;
	const DEFAULT_DOMAIN = null;
	const DEFAULT_SECURE = null;
	const DEFAULT_HTTP_ONLY = null;
}
