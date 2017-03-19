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

/**
 * A session handler that persist data during run time.
 */
class RuntimeSessionHandler extends \SessionHandler
{
	/**
	 * Register a new instance of the class as save handler.
	 *
	 * @codeCoverageIgnore
	 */
	static public function register()
	{
		session_set_save_handler(new static);
	}

	/**
	 * @var mixed
	 */
	private $data;

	/**
	 * @inheritdoc
	 */
	public function close()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function destroy($session_id)
	{
		$this->data = null;

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gc($maxlifetime)
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function open($save_path, $session_id)
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function read($session_id)
	{
		return $this->data;
	}

	/**
	 * @inheritdoc
	 */
	public function write($session_id, $session_data)
	{
		$this->data = $session_data;

		return true;
	}
}
