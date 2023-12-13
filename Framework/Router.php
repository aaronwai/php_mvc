<?php

namespace Framework;

use App\Controllers\ErrorController;

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

    public function registerRoute($method,$uri, $action)
    {
        // separate the $action into 2 parts, using list() to destructure into 2 variables
        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod
        ]; 
    }
    /**
     *  add a get route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller) {
       $this->registerRoute('GET', $uri, $controller);
    }

     /**
     *  add a Post route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller) {
        $this->registerRoute('POST', $uri, $controller);
    
    }

     /**
     *  add a put route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller) {
        $this->registerRoute('PUT', $uri, $controller);
    }

     /**
     *  add a delete route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller) {
        $this->registerRoute('DELETE', $uri, $controller);
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