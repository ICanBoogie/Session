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
	const OPTION_ID = 'id';
	const OPTION_NAME = 'name';
	const OPTION_CACHE_LIMITER = 'cache_limiter';
	const OPTION_CACHE_EXPIRE = 'cache_expire';
	const OPTION_MODULE_NAME = 'module_name';
	const OPTION_SAVE_PATH = 'save_path';
	const OPTION_COOKIE_PARAMS = 'cookie_params';
	const OPTION_SEGMENT_NAME = 'segment_name';

	const DEFAULT_ID = null;
	const DEFAULT_NAME = null;
	const DEFAULT_CACHE_LIMITER = null;
	const DEFAULT_CACHE_EXPIRE = null;
	const DEFAULT_MODULE_NAME = 'files';
	const DEFAULT_SAVE_PATH = null;
	const DEFAULT_SEGMENT_NAME = 'icanboogie';
}
