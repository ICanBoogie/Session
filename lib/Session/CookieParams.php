<?php

namespace ICanBoogie\Session;

/**
 * Cookie params.
 */
final readonly class CookieParams
{
    /**
     * @param int $lifetime Lifetime of the cookie in seconds. `0` means until the browser is closed.
     * @param string $path Path on the domain where the cookie will work.
     * @param string $domain Cookie domain.
     * @param bool $secure Whether the cookie should only be sent over secure connections.
     * @param bool $http_only Whether the cookie should only be accessible through the HTTP protocol.
     * @param string $same_site Value of the `SameSite` attribute, e.g. `Lax`, `Strict` or `None`.
     */
    public function __construct(
        public int $lifetime = 0,
        public string $path = '/',
        public string $domain = '',
        public bool $secure = false,
        public bool $http_only = false,
        public string $same_site = '',
    ) {
    }

    /**
     * Restores an instance exported with `var_export()`.
     *
     * @param array{
     *     lifetime: int,
     *     path: string,
     *     domain: string,
     *     secure: bool,
     *     http_only: bool,
     *     same_site: string
     * } $an_array
     */
    public static function __set_state(array $an_array): self
    {
        return new self(...$an_array);
    }

    /**
     * Creates an instance from the array returned by `session_get_cookie_params()`.
     *
     * @param array{
     *     lifetime?: int,
     *     path?: string,
     *     domain?: string,
     *     secure?: bool,
     *     httponly?: bool,
     *     samesite?: string
     * } $params
     */
    public static function from_array(array $params): self
    {
        return new self(
            $params['lifetime'] ?? 0,
            $params['path'] ?? '/',
            $params['domain'] ?? '',
            $params['secure'] ?? false,
            $params['httponly'] ?? false,
            $params['samesite'] ?? '',
        );
    }
}
