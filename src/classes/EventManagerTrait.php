<?php
namespace Feathers;

use \Feathers\EventManager;

trait EventManagerTrait
{
	protected $manager;

	// public function setEventManager(EventManager $manager)
	// {
	// 	$this->manager = $manager;
	// }
	public function getEventManager()
	{
		if ($this->manager === null) {
			$this->manager = new EventManager;
		}
		return $this->manager;
	}

	public function on(string $eventname, \Closure $listener)
	{
		$this->getEventManager()->on($eventname, $listener);
	}

	public function emit(string $eventname, array $data)
	{
		return $this->getEventManager()->emit($eventname, $data);
	}
}