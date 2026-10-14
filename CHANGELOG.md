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
