<?php
namespace Feathers;

class Memory implements \ArrayAccess, \Iterator, \Countable
{
	protected $data;
	protected $position = 0;
	protected $keys = [];
	
	public function __construct(array $data = [])
	{
		$this->data = $data;
	}

	public function offsetExists($offset): bool
	{
		return !empty($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		if (is_array($this->data[$offset])) {
			$this->data[$offset] = new Memory($this->data[$offset]);
		}
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value): void
	{
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset): void
	{
		unset($this->data[$offset]);
	}

	public function current()
	{
		return $this->data[$this->key()];
	}

	public function key()
	{
		return $this->keys[$this->position];
	}

	public function next()
	{
		++$this->position;
	}

	public function rewind()
	{
		$this->position = 0;
		$this->keys = array_keys($this->data);
	}

	public function valid()
	{
		return isset($this->keys[$this->position]);
	}

	public function count()
	{
		return sizeof($this->data);
	}

}