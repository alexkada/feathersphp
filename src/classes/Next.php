<?php
namespace Feathers;

class Next
{
	protected $middleware;
	protected $paramed = false;

	function __construct($middleware)
	{
		$this->middleware = $middleware;
	}

	function __invoke()
	{
		if($this->middleware->finished or $this->middleware->getSize() === 0) {
			$size = sizeof($this->middleware->params);
			if($size > 0) {
				unset($this->middleware->params[$size - 1]);
			}
			return $this->middleware->params;
		}
		if(!$this->paramed) {
			$this->middleware->params[] = $this;
		}
		$middleware = $this->middleware->getMiddleware();
		//$params = $this->middleware->params;
		//$params[] = $this;
		return call_user_func_array($middleware, $this->middleware->params); //$middleware($this->middleware->request, $this->middleware->response,$this);
	}
}