<?php
require_once "vendor/autoload.php";
require_once "src/functions.php";
use \Illuminate\Database\Capsule\Manager;
use \Illuminate\Events\Dispatcher as Disp;
use \Illuminate\Container\Container;

use Zend\Diactoros\Response;

use Feathers\Config;
use Feathers\Application;
use Feathers\Service;
use Models\Order;
use Feathers\EloquentAdapter;
use Feathers\MixinTrait;
// $status = $routeInfo[0];
// $handler = $routeInfo[1];
// $params = $routeInfo[2];
//query
//id
//data


//$mixin = new MixinTrait();

//$mixin->test();


//$hook = loadHook("beforeFindOrderHook");

$capsule = new Manager;



$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => '127.0.0.1',
    'database'  => 'payment',
    'username'  => 'postgres',
    'password'  => '123',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
],"payment");

// // Set the event dispatcher used by Eloquent models... (optional)

 $capsule->setEventDispatcher(new Disp(new Container));

// // Make this Capsule instance available globally via static methods... (optional)
 $capsule->setAsGlobal();

// // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
 $capsule->bootEloquent();
// $orders = Order::all();
// print_r($orders);

//$adapter->remove(99);
// $adapter->create([
// "name" => "Test1",
// "status_id" => 1,
// "client_id" => 2
// ]);

$app = new Application(__DIR__);
$app->registerService("orders",new EloquentAdapter(new Order));
//$service = new Service("orders", $app);
//$service->registerModel($adapter);

$app->run();

// $data = [
// "level1" => [
// 	"level2" => [
// 		"level3" => 'data'
// 		]
// 	]
// ];

// $config = new Config($data);

// $q =  $config['level1']['level2']['level3'];