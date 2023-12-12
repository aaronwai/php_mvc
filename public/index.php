<?php 
require '../helpers.php';

require basePath('Router.php');
require basePath('Database.php');

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