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
    public function route($uri, $method) {
        foreach($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                // extract controller and controller method
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];
                // Instatiate the controller and call the method
                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod();
                return;
            }
        }
        ErrorController::notFound();
    }
} 