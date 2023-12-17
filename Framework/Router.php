<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router {
    protected $routes = [];

    /**
     *  Add a new route
     * 
     * @param string $method
     * @param string $uri
     * @param string $action
     * @return void
     */

    public function registerRoute($method,$uri, $action, $middleware=[])
    {
        // separate the $action into 2 parts, using list() to destructure into 2 variables
        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware'=> $middleware
        ]; 
    }
    /**
     *  add a get route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function get($uri, $controller, $middleware=[]) {
       $this->registerRoute('GET', $uri, $controller, $middleware);
    }

     /**
     *  add a Post route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function post($uri, $controller, $middleware=[]) {
        $this->registerRoute('POST', $uri, $controller, $middleware);
    
    }

     /**
     *  add a put route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function put($uri, $controller,$middleware=[]) {
        $this->registerRoute('PUT', $uri, $controller, $middleware);    
    }

     /**
     *  add a delete route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller,$middleware=[]) {
        $this->registerRoute('DELETE', $uri, $controller, $middleware);
    }

   
    /**
     *  Route the request
     * 
     * @param string $uri
     * @param string $method
     * @return void
     *  
     */
    public function route($uri) {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        // check for _method input
        if ($requestMethod == 'POST' && isset($_POST['_method'])) {
            // override the request method with the value of _method
            $requestMethod = strtoupper($_POST['_method']);
        }
        foreach ($this->routes as $route) {

            // split the current URI into segments
            $uriSegments = explode('/', trim($uri,'/'));

            // split the current URI into segments, this is defined in the routes.php
            $routeSegments = explode('/', trim($route['uri'],'/')); 

            $match = true;

            // check if the number of segments matches
            if (count($uriSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)) {
                
                $params = [];
                
                $match = true;
                
                for ($i = 0; $i < count($uriSegments); $i++) {
                    // if the uri's do not match and there is no param 
                    if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }

                    // check for the param and add to $params array
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i],$matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }

                if ($match) {
                    
// insert middleware, by looping the middleware array
                    foreach($route['middleware'] as $middleware) {
                        (new Authorize())->handle($middleware);
                    }
                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params);
                    return;
                }
            }
        }
        ErrorController::notFound();
    }


} 