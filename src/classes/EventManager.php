<?php
namespace Feathers;

class EventManager
{
	protected $events = [];

	public function __construct()
	{

	}

	public function on(string $eventname, \Closure $listener)
	{
		if (empty($this->events[$eventname])) {
			$this->events[$eventname] = [];
		}
		$this->events[$eventname][$listener] = $listener;
	}

	public function emit(string $eventname, array $data)
	{
		if (empty($this->events[$eventname]) or sizeof($this->events[$eventname]) === 0) {
			return [];
		}
		$result = [];
		foreach ($this->events[$eventname] as $key => $value) {
			$result[] = call_user_func_array($value, $data);
		}
		return $result;
	}

	public function removeListener(string $eventname, \Closure $listener)
	{
		if (empty($this->events[$name][$listener])) {
			return false;
		}
		return unset($this->events[$eventname][$listener]);
	}
}