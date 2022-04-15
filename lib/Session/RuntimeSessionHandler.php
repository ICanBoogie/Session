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

use SessionHandler;

/**
 * A session handler that persist data during run time.
 */
final class RuntimeSessionHandler extends SessionHandler
{
	/**
	 * Register a new instance of the class as save handler.
	 *
	 * @codeCoverageIgnore
	 */
	public static function register(): bool
	{
		return session_set_save_handler(new self());
	}

	private mixed $data;

	/**
	 * @inheritdoc
	 */
	public function close(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function destroy(string $id): bool
	{
		$this->data = null;

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gc(int $max_lifetime): int|false
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function open(string $path, string $name): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function read(string $id): string|false
	{
		return $this->data ?? false;
	}

	/**
	 * @inheritdoc
	 */
	public function write(string $id, string $data): bool
	{
		$this->data = $data;

		return true;
	}
}
