<?php

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

	const DEFAULT_OPTION_LIFETIME = 0;
	const DEFAULT_OPTION_PATH = null;
	const DEFAULT_OPTION_DOMAIN = null;
	const DEFAULT_OPTION_SECURE = null;
	const DEFAULT_OPTION_HTTP_ONLY = null;
}
