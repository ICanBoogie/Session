<?php

namespace ICanBoogie;

use ICanBoogie\Session\CookieParams;

/**
 * Session options.
 *
 * A `null` value means that the current PHP setting is left untouched.
 */
final readonly class SessionOptions
{
    /**
     * @param string|null $id Session id. An empty string is ignored.
     * @param string|null $name Session name.
     * @param string|null $cache_limiter Cache limiter.
     * @param int|null $cache_expire Cache expire, in minutes.
     * @param string|null $module_name Session module name.
     * @param string|null $save_path Session save path.
     * @param CookieParams|null $cookie_params Session cookie parameters.
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $cache_limiter = null,
        public ?int $cache_expire = null,
        public ?string $module_name = null,
        public ?string $save_path = null,
        public ?CookieParams $cookie_params = null,
    ) {
    }

    /**
     * Restores an instance exported with `var_export()`.
     *
     * @param array{
     *     id?: string|null,
     *     name?: string|null,
     *     cache_limiter?: string|null,
     *     cache_expire?: int|null,
     *     module_name?: string|null,
     *     save_path?: string|null,
     *     cookie_params?: CookieParams|null
     * } $an_array
     */
    public static function __set_state(array $an_array): self
    {
        return new self(...$an_array);
    }
}
