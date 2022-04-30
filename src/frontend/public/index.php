<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;

(new \Phalcon\Debug())->listen(1, 1);

$config = new Config([]);
require_once('../vendor/autoload.php');

$_SERVER["REQUEST_URI"] = str_replace("frontend/", "", $_SERVER["REQUEST_URI"]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH);

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/component'
    ]
);
$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
$container->set(
    'token',
    function () {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOi8vZXhhbXBsZS5jb20iLCJpYXQiOjEzNTY5OTk1MjQsImV4cCI6Mzk2MjkzODk0ODgsInJvbGUiOiJ1c2VyIiwiaWQiOiI2MjZiYWY1Y2E5MzY3YmZhZjQwZGI4OTIifQ.jArgouv_4D64ZbrbKUB5FZo_n4Os5sXA6Pd-3s1ddPI";
        return $token;
    }
);
$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);
$profiler = new \Fabfuel\Prophiler\Profiler();
$toolbar = new \Fabfuel\Prophiler\Toolbar($profiler);
$toolbar->addDataCollector(new \Fabfuel\Prophiler\DataCollector\Request());
// echo $toolbar->render();
$application = new Application($container);

$container->set(
    'mongo',
    function () {
        $mongo = new \MongoDB\Client("mongodb://mongo", array("username" => 'root', "password" => "password123"));
        // mongo "mongodb+srv://sandbox.g819z.mongodb.net/myFirstDatabase" --username root

        return $mongo->frontend;
    },
    true
);
try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
