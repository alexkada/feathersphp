<?php
namespace Feathers;
use \Feathers\Application;
use \Feathers\Service;

class HookManager
{
	protected $hooks = [
	"all" => [
		"find" => [],
		"get" => [],
		"create" => [],
		"update" => [],
		"patch" => [],
		"remove" => []
	],	
	"before" => [
		"find" => [],
		"get" => [],
		"create" => [],
		"update" => [],
		"patch" => [],
		"remove" => []
	],
	"after" => [
		"find" => [],
		"get" => [],
		"create" => [],
		"update" => [],
		"patch" => [],
		"remove" => []
	],
	"error" => [
		"find" => [],
		"get" => [],
		"create" => [],
		"update" => [],
		"patch" => [],
		"remove" => []
	]
	];

	public function __construct(Service $service, $hooks = null)
	{

		$path = rtrim($service->getApp()->dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."services".DIRECTORY_SEPARATOR.$service->getName().DIRECTORY_SEPARATOR."hooks.php";
		if (file_exists($path)) {
			$this->hooks = require($path);
		}

		if (!is_null($hooks)) {
			$this->hooks = $hooks;
		}
		//var_dump($this->hooks);
	}

	public function register(\Closure $hook, $method, $type = "all")
	{
		if(empty($this->hooks[$type][$method])) {
			return false;
		}
		$this->hooks[$type][$method][] = $hook;
	}

	public function fire(&$context, $method, $type)
	{
		if (empty($this->hooks[$type][$method])) {
			return false;
		}
		$hooks = $this->hooks[$type][$method];
		$size = sizeof($hooks);
		if ($size === 0) {
			return true;
		}

		for ($i = 0; $i < $size; $i++) {
			$hooks[$i]($context);
		}
	}

}