# CHANGELOG

## v7.0.0

### New Requirements

PHP 8.4+

`icanboogie/accessor` is no longer required.

### New features

None

### Backward Incompatible Changes

- Setting `id`, `name`, `cache_limiter`, `cache_expire`, `module_name` or `save_path` to `false` throws an
  `InvalidArgumentException`. Those properties still read as `false` when sessions are disabled.
- Writing to a segment of the segment collection throws `ICanBoogie\Session\SegmentNotWritable`, instead of
  `ICanBoogie\OffsetNotWritable`, which came from `icanboogie/common`. The new exception extends `LogicException`,
  as the old one did.
- `RuntimeSessionHandler::gc()` returns `int`.
- `ICanBoogie\Session\CookieParams` is now a `final readonly` value object, instead of an interface. The `OPTION_*`,
  `DEFAULT_*` and `DEFAULTS` constants are replaced by promoted properties: `lifetime`, `path`, `domain`, `secure`,
  `http_only` and `same_site`. `Session::$cookie_params` and the `cookie_params` option are now `CookieParams`
  instances, instead of arrays. Use `CookieParams::from_array()` to convert the result of
  `session_get_cookie_params()`.

### Deprecated Features

None

### Other Changes

- Replaced PHP Code Sniffer with PHP-CS-Fixer.
- The license is BSD-3-Clause, as for the other ICanBoogie packages.
- Tooling updated: PHPUnit 13.4, PHPStan 2.3, CI tests the lowest and the latest dependencies.
- icanboogie/accessor replaced with hook properties (the API remains the same)



## v6.0.0

### New Requirements

- PHP 8.1+

### New features

None

### Backward Incompatible Changes

None

### Deprecated Features

None

### Other Changes

None
