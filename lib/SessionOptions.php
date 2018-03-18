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

/**
 * Session options.
 */
interface SessionOptions
{
	public const OPTION_ID = 'id';
	public const OPTION_NAME = 'name';
	public const OPTION_CACHE_LIMITER = 'cache_limiter';
	public const OPTION_CACHE_EXPIRE = 'cache_expire';
	public const OPTION_MODULE_NAME = 'module_name';
	public const OPTION_SAVE_PATH = 'save_path';
	public const OPTION_COOKIE_PARAMS = 'cookie_params';

	public const DEFAULT_ID = null;
	public const DEFAULT_NAME = null;
	public const DEFAULT_CACHE_LIMITER = null;
	public const DEFAULT_CACHE_EXPIRE = null;
	public const DEFAULT_MODULE_NAME = 'files';
	public const DEFAULT_SAVE_PATH = null;

	public const DEFAULTS = [

		self::OPTION_ID            => self::DEFAULT_ID,
		self::OPTION_NAME          => self::DEFAULT_NAME,
		self::OPTION_CACHE_LIMITER => self::DEFAULT_CACHE_LIMITER,
		self::OPTION_CACHE_EXPIRE  => self::DEFAULT_CACHE_EXPIRE,
		self::OPTION_MODULE_NAME   => self::DEFAULT_MODULE_NAME,
		self::OPTION_SAVE_PATH     => self::DEFAULT_SAVE_PATH,

	];
}
