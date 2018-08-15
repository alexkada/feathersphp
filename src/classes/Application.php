<?php
namespace Feathers;

use Feathers\{Middleware, Config, Service, ModelAdapter};
use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector as RouteCollector;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class Application
{
	protected $path;
	protected $request;
	protected $response;
	protected $method;
	protected $middleware;
	protected $globalMiddleware;
	protected $router;
	protected $config = [];
	protected $services =[];
	public $dir;

	//public function __construct(ServerRequest $request, Response $response, array $config = [])
	public function __construct(string $dir, array $config = [])
	{
		$this->dir = $dir;
		$this->config = new Config($config);
		$this->request = $request = \Zend\Diactoros\ServerRequestFactory::fromGlobals(
		    $_SERVER,
		    $_GET,
		    null,
		    $_COOKIE,
		    $_FILES
		);
		$this->response = new Response;
		$this->path = $request->getUri()->getPath();
		$this->method = $request->getMethod();
		$this->router = new RouteCollector(new RouteParser, new DataGenerator);
		$this->params = $request->getQueryParams();
		$headers = $request->getHeaders();
		$this->body = json_decode($request->getBody()->getContents(),true);
		$attrs = $request->getAttributes();
		$this->request = $this->request->withAttribute("body",json_decode($request->getBody()->getContents(),true));
		$this->globalMiddleware = new Middleware($this->request, $this->response);
	}

	public function registerService(string $name, ModelAdapter $model)
	{
		$service = new Service($name, $this);
		$service->registerModel($model);
		$this->services[$name] = $service;
	}

	public function getService(string $name)
	{
		return $this->services[$name];
	}
	public function set($key, $value)
	{
		$this->config[$key] = $value;
	}

	public function get($key)
	{
		return $this->config[$key];
	}
	public function use(\Closure $callback)
	{
		$this->globalMiddleware->add($callback);	
	}

	public function _use(string $method, string $path, \Closure $callback) {
		if(empty($this->middleware[$method])){
			$this->middleware[$method] = [];
		}
		if(empty($this->middleware[$method][$path])) {
			$this->middleware[$method][$path] = new Middleware($this->request, $this->response);
		}
		if($this->middleware[$method][$path]->getSize() === 0) {
			$this->router->addRoute($method,$path,$path);
		}
		$this->middleware[$method][$path]->add($callback);
		return $this;
	}

	public function _get(string $path, \Closure $callback){
		return $this->_use("GET", $path, $callback);
	}

	public function _post(string $path, \Closure $callback){
		return $this->_use("POST", $path, $callback);
	}

	public function _patch(string $path, \Closure $callback){
		return $this->_use("PATCH", $path, $callback);
	}

	public function _put(string $path, \Closure $callback){
		return $this->_use("PUT", $path, $callback);
	}

	public function _delete(string $path, \Closure $callback){
		return $this->_use("DELETE", $path, $callback);
	}

	function send()
	{
	    $http_line = sprintf('HTTP/%s %s %s',
	        $this->response->getProtocolVersion(),
	        $this->response->getStatusCode(),
	        $this->response->getReasonPhrase()
	    );
	    header($http_line, true, $this->response->getStatusCode());
	    foreach ($this->response->getHeaders() as $name => $values) {
	        foreach ($values as $value) {
	            header("$name: $value", false);
	        }
	    }
	    $body = (string) $this->response->getBody();
	    //var_dump($body);
	    echo $body;
	}
	public function usePHPResponse()
	{
		$this->response = $this->response->withHeader("Content-Type","application/json");
		$this->send();
	}

	public function run()
	{
		$dispatcher = new Dispatcher($this->router->getData());
		$routeInfo = $dispatcher->dispatch($this->method, rtrim($this->path, "/"));
		$status = $routeInfo[0];
		$params =   isset($routeInfo[2]) ? $routeInfo[2] : [];
		$handler =  isset($routeInfo[1]) ? $routeInfo[1] : "";
		if(isset($params['id'])) {
			$this->id = $params['id'];
			$this->request = $this->request->withAttribute("id", $this->id);
		}
		/*
		    const NOT_FOUND = 0;
    		const FOUND = 1;
    		const METHOD_NOT_ALLOWED = 2;
		 */

		//if($status === 1) echo "STATUS: FOUND<br>";
		if($status === 0) echo "STATUS: NOT_FOUND<br>";
		if($status === 2) echo "STATUS: METHOD_NOT_ALLOWED<br>";
		if($status === 1) {
			$this->globalMiddleware->handle();
			$this->middleware[$this->method][$handler]->handle();
		}
		
		$this->usePHPResponse();
	}
}