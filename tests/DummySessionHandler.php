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
 * A session handler doing nothing.
 */
class DummySessionHandler extends \SessionHandler
{
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
