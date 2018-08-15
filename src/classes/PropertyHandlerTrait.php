<?php
namespace Feathers;

trait PropertyHandlerTrait
{
	protected $handlers = [];
	protected $__properties = [];
	protected $storageName = "__properties";

	public function setPropertyStorageName(string $name)
	{
		$this->storageName = $name;
	}

	public function getPropertyStorageName()
	{
		return $this->storageName;
	}

	public function propertyHandlerRegister(string $propertyName, \Closure $handler)
	{
		if (empty($this->handlers[$propertyName])) {
			$this->handlers[$propertyName] = [];
		}
		$this->handlers[$propertyName][$handler] = $handler;
	}

	public function propertyHandlerRemove(string $propertyName, \Closure $handler)
	{
		if (empty($this->handlers[$propertyName][$handler])) {
			return false;
		}
		unset($this->handlers[$propertyName][$handler]);
	}

	public function __set(string $key, $value)
	{
		if (!empty($this->handlers[$key]) and sizeof($this->handlers[$key]) > 0) {
			foreach ($this->handlers[$key] as $k => $v) {
				$value = call_user_func_array($v, $value);
			}
		}

		$this->{$this->getPropertyStorageName()}[$key] = $value;
	}

	public function __get(string $key)
	{
		return $this->{$this->getPropertyStorageName()}[$key];
	}


}