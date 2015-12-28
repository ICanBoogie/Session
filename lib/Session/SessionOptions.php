<?php

namespace ICanBoogie\Session;

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
	const OPTION_SEGMENT = 'segment';

	const DEFAULT_OPTION_ID = null;
	const DEFAULT_OPTION_NAME = null;
	const DEFAULT_OPTION_CACHE_LIMITER = null;
	const DEFAULT_OPTION_CACHE_EXPIRE = null;
	const DEFAULT_OPTION_MODULE_NAME = 'files';
	const DEFAULT_OPTION_SAVE_PATH = null;
	const DEFAULT_OPTION_SEGMENT = 'icanboogie';
}
