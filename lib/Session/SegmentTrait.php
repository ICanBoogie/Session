<?php

namespace ICanBoogie\Session;

trait SegmentTrait
{
	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		return isset($this->get_segment()[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function &offsetGet($offset)
	{
		return $this->get_segment()[$offset];
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		$this->get_segment()[$offset] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		unset($this->get_segment()[$offset]);
	}

	/**
	 * Starts a new session or reuse the current one.
	 */
	abstract protected function &get_segment();
}
