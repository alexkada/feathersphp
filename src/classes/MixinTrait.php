<?php
namespace Feathers;

trait MixinTrait
{
	protected $mixins = [];

	public function __call(string $name, array $params)
	{
		if (empty($this->mixins[$name])) {
			return null;
		}
		$callback = $this->mixins[$name];
		$callback = \Closure::bind($callback,$this,get_called_class());
		return call_user_func_array($callback, $params);
	}

	public function mixin(string $name, \Closure $callback)
	{
		$this->mixins[$name] = $callback;
	}

	public function removeMixin(string $name)
	{
		if (empty($this->mixins[$name])) {
			return false;
		}
			unset($this->mixins[$name]);
	}
}