<?php 
require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

use Framework\Router;

// spl_autoload_register(function ($class) {
//     $path=basePath('Framework/' . $class . '.php');
//     if (file_exists($path)) {
//         require $path;
//     }
// });

// create new router object
$router = new Router();
// get routes
$routes = require basePath('routes.php');

// get current uri path and method (return get or post)
// refactor only get the path part and filter out query part
$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
// route the request
$router->route($uri,$method);