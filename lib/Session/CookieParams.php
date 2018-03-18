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
	public const OPTION_LIFETIME = 'lifetime';
	public const OPTION_PATH = 'path';
	public const OPTION_DOMAIN = 'domain';
	public const OPTION_SECURE = 'secure';
	public const OPTION_HTTP_ONLY = 'httponly';

	public const DEFAULT_LIFETIME = 0;
	public const DEFAULT_PATH = null;
	public const DEFAULT_DOMAIN = null;
	public const DEFAULT_SECURE = null;
	public const DEFAULT_HTTP_ONLY = null;

	public const DEFAULTS = [

		self::OPTION_LIFETIME => self::DEFAULT_LIFETIME,
		self::OPTION_PATH => self::DEFAULT_PATH,
		self::OPTION_DOMAIN => self::DEFAULT_DOMAIN,
		self::OPTION_SECURE => self::DEFAULT_SECURE,
		self::OPTION_HTTP_ONLY => self::DEFAULT_HTTP_ONLY,

	];
}
