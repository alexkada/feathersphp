<?php
namespace Feathers;
use \Feathers\MixinTrait;

class Config implements \ArrayAccess
{
	use MixinTrait;
	
	protected $config;

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	function offsetExists($offset): bool
	{
		return !empty($this->config[$offset]);
	}
	//$config['file']['redirect']['implode']
	function offsetGet($offset)
	{
		if (is_array($this->config[$offset])) {
			$this->config[$offset] = new Config($this->config[$offset]);
		}
		return $this->config[$offset];
	}

	function offsetSet($offset, $value): void
	{
		$this->config[$offset] = $value;
	}

	function offsetUnset($offset): void
	{
		unset($this->config[$offset]);
	}
}