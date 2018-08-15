<?php
namespace Feathers;
use \Feathers\Application;
use \Feathers\ModelAdapter;
use \Feathers\EventManagerTrait;
use \Feathers\MixinTrait;
use \Feathers\HookManager;

class Service
{
	use EventManagerTrait, MixinTrait;

	protected $app;
	protected $name;
	protected $beforeHooks = [];
	protected $afterHooks = [];
	protected $allHooks = [];
	protected $errorHooks = [];
	protected $model;
	protected $hooks;

	public function getHookManager()
	{
		return $this->hooks;
	}
	public function registerModel(ModelAdapter $model): void
	{
		$this->model = $model;
	}
	
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setApp(Application $app): bool
	{
		$this->app = $app;
	}

	public function getApp(): Application
	{
		return $this->app;
	}

	public function __construct(string $name, Application $app)
	{
		
		$this->setName($name);
		$this->app = $app;
		$this->hooks = new HookManager($this);
		$this->setup();
	}

	public function find(array $query): array
	{
		$result = $this->model->find($query);
		$total = sizeof($result);
		$limit = 10;
		$skip = 0;
		return ["total" => $total, "limit" => $limit, "skip" => $skip, "data" => $result];
	}

	public function get(int $id, array $query): array
	{
		$result = $this->model->get($id, $query);
		return $result;
	}

	public function create(array $data): array
	{
		$result = $this->model->create($data);
		return $result;
	}

	public function patch(int $id, array $data, array $query): array
	{
		$result = $this->model->patch($id, $data, $query);
		return $result;
	}

	public function update(int $id, array $data, array $query): array
	{
		$result = $this->model->update($id, $data, $query);
		return $result;
	}

	public function remove(int $id, array $query): array
	{
		$result = $this->model->remove($id, $query);
		return $result;
	}

	public function setup()
	{
		$service = $this;
		$this->app->_get('/'.$this->name,function($request, $response, $next) use($service){

			$context = [];
			$query = $request->getQueryParams();
			$service->getHookManager()->fire($context, "find", "all");
			$service->getHookManager()->fire($context, "find", "before");
			$result = $service->find($query);
			$service->getHookManager()->fire($context, "find", "after");
			$result['method_type'] = "Find";
			$response->getBody()->write(json_encode($result));

			return $next();
		});
		$this->app->_get('/'.$this->name.'/{id}',function($request, $response, $next) use($service){
			$context = [];
			$query = $request->getQueryParams();
			$id = $request->getAttribute("id");
			$service->getHookManager()->fire($context, "get", "all");
			$service->getHookManager()->fire($context, "get", "before");
			$result = $service->get($id, $query);
			$service->getHookManager()->fire($context, "get", "after");
			$result = json_encode(["method_type" => "Get", "data" => $result]);
			$response->getBody()->write($result);
			return $next();
		});
		$this->app->_post('/'.$this->name,function($request, $response, $next) use($service){
			$context = [];
			$data = $request->getAttribute("body");
			$service->getHookManager()->fire($context, "create", "all");
			$service->getHookManager()->fire($context, "create", "before");
			$result = $service->create($data);
			$service->getHookManager()->fire($context, "create", "after");
			$result = json_encode(["method_type" => "Create", "data" => $result]);
			$response->getBody()->write($result);
			return $next();
		});
		$this->app->_patch('/'.$this->name.'/{id}',function($request, $response, $next) use($service){
			$context = [];
			$query = $request->getQueryParams();
			$id = $request->getAttribute("id");
			$data = $request->getAttribute("body");
			$service->getHookManager()->fire($context, "patch", "all");
			$service->getHookManager()->fire($context, "patch", "before");
			$result = $service->patch($id, $data, $query);
			$service->getHookManager()->fire($context, "patch", "after");
			$result = json_encode(["method_type" => "Patch", "data" => $result]);
			$response->getBody()->write($result);
			
			return $next();
		});
		$this->app->_put('/'.$this->name.'/{id}',function($request, $response, $next) use($service){
			$context = [];
			$query = $request->getQueryParams();
			$id = $request->getAttribute("id");
			$data = $request->getAttribute("body");
			$service->getHookManager()->fire($context, "update", "all");
			$service->getHookManager()->fire($context, "update", "before");
			$result = $service->update($id, $data, $query);
			$service->getHookManager()->fire($context, "update", "after");
			$result = json_encode(["method_type" => "Update", "data" => $result]);
			$response->getBody()->write($result);
			return $next();
		});
		$this->app->_delete('/'.$this->name.'/{id}',function($request, $response, $next) use($service){
			$context = [];
			$query = $request->getQueryParams();
			$id = $request->getAttribute("id");

			$service->getHookManager()->fire($context, "remove", "all");
			$service->getHookManager()->fire($context, "remove", "before");
			$result = $service->remove($id, $query);
			$service->getHookManager()->fire($context, "remove", "after");

			$result = json_encode(["method_type" => "Remove", "data" => $result]);
			$response->getBody()->write($result);
			//echo json_encode(["method_type" => "Remove", "data" => $result]);
			return $next();
		});			
	}
}