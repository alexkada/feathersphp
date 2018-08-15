<?php
namespace Feathers;

use \Feathers\Next;

class Middleware
{
	protected $context = [];
	//public $request;
	//public $response;
	public $finished = false;
	protected $position = 0;
	protected $size = 0;
	public $params = [];
	public function getSize()
	{
		return $this->size;
	}
	// public function __construct($request, $response) {
	// 	$this->request = $request;
	// 	$this->response = $response;
	// }
	public function __construct(&...$params)
	{
		$this->params = $params;
		//$this->params[1] = $this->params[1]->withHeader("Server","feathers");
	}
	public function add(\Closure $middleware)
	{
		$this->context[] = $middleware;
		$this->size += 1;
	}

	public function getMiddleware()
	{
		$middleware = $this->context[$this->position];
		$this->position += 1;
		if(($this->size - 1) < $this->position) {
			$this->finished = true;
		}
		return $middleware;
	}

	public function handle()
	{
		$next = new Next($this);
		return $next();
	}
}
