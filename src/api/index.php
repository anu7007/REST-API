<?php

use Phalcon\Mvc\Micro;
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Events\Manager as EventsManager;

require_once "./vendor/autoload.php";

$loader = new Loader();
$loader->registerNamespaces(
    [
        'Api\Handlers' => './handlers',
        'MyEventsHandler' => './listener'
    ]
);

$loader->register();

$product = new Api\Handlers\Product();
$order = new Api\Handlers\Order();

$container = new FactoryDefault();

$app = new Micro($container);

$app->before(
    function () use ($app) {
        if (!str_contains($_SERVER['REQUEST_URI'], 'gettoken') && !str_contains($_SERVER['REQUEST_URI'], 'order')) {
            $token = $app->request->getQuery("token");
            if (!$token) {
                echo 'Token not provided"';
                die;
            }
            $key = 'example_key';
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Firebase\JWT\ExpiredException $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                die;
            }
            if ($decoded->role != 'admin') {
                echo 'Permission Denied';
                die;
            }
        }
    }
);

$app->get(
    '/invoices/view/{id}/{where}/{limit}/{page}',
    [
        $product,
        'get'
    ]
);

$app->get(
    '/api/product/get/{per_page}/{page}',
    [
        $product,
        'getProducts'
    ]
);

$app->get(
    '/api/product/list',
    [
        $product,
        'productlist'
    ]
);

$app->get(
    '/api/product/search/{keyword}',
    [
        $product,
        'searchProducts'
    ]
);

$app->get(
    '/api/gettoken',
    [
        $product,
        'gettoken'
    ]
);

$app->post(
    '/api/order/placeorder',
    [
        $order,
        'placeorder'
    ]
);

$app->put(
    '/api/order/updateorder',
    [
        $order,
        'updateorder'
    ]
);

$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => 'root', "password" => "password123"));
        // mongo "mongodb+srv://sandbox.g819z.mongodb.net/myFirstDatabase" --username root

        return $mongo->store;
    },
    true
);
$eventsmanager = new EventsManager();

$eventsmanager->attach(
    'myevent',
    new \MyEventsHandler\NotificationsListeners()
);

$container->set(
    'events',
    $eventsmanager
);

try {
    $app->handle(
        $_SERVER['REQUEST_URI']
    );
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
