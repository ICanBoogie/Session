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
	static public function register(): bool
	{
		return session_set_save_handler(new self);
	}

	/**
	 * @var mixed
	 */
	private $data;

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
	public function destroy($id): bool
	{
		$this->data = null;

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gc($max_lifetime): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function open($path, $name): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function read($id)
	{
		return $this->data;
	}

	/**
	 * @inheritdoc
	 */
	public function write($id, $data): bool
	{
		$this->data = $data;

		return true;
	}
}
