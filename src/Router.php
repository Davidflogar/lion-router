<?php

namespace Lion\LionRouter;

use Lion\LionRouter\Functions\Call;
use Lion\LionRouter\Helpers\Arrays;
use Lion\LionRouter\RouteChecker\RouteChecker;

/**
 * Main router.
 */
class Router
{
    /**
     * The array where the routes will be saved.
     * 
     * @var array
     */
    private $routes;

    /**
     * The config of the router.
     * 
     * @var array
     */
    private $config;

    /**
     * The array where the middlewares will be saved.
     * 
     * @var array
     */
    private $middlewares = [];

    /**
     * The array where the parameters by type will be saved.
     * 
     * @var array
     */
    private $params_by_type = [];

    /**
     * Calls the "not_found" function
     */
    public function not_found_function()
    {
        // create the object
        $object =  new $this->config["not_found"][0];

        // call the function
        $not_found_action = $this->config['not_found'][1];

        $object->$not_found_action();

        // set the header and status code
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        http_response_code(404);
    }

    /**
     * Calls the "not allowed" function
     */
    public function not_allowed_function()
    {
        // create the object
        $object =  new $this->config["not_allowed"][0];

        // call the function
        $not_allowed_action = $this->config['not_allowed'][1];

        $object->$not_allowed_action();

        // set the header and status code
        header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed");
        http_response_code(405);
    }

    /**
     * Constructor.
     */
    public function __construct(array $array, array $not_found, array $not_allowed)
    {
        // create the error message
        $error = "Arguments to create a new Router are invalid. Check that the classes and the method exists and they are valid.";

        // validate the arguments
        Arrays::object_in_array($not_found, $error);
        Arrays::object_in_array($not_allowed, $error);

        // set all the routes
        $this->routes = $array;

        // set the url not found function
        $this->config['not_found'] = $not_found;

        // set the method not allowed function
        $this->config['not_allowed'] = $not_allowed;

    }

    /**
     * Loads a new middleware.
     * 
     * @param string $name
     * 
     * The name of the middleware. For example: 'auth'
     * 
     * @param array $middleware
     * 
     * The middleware, it must be passed as an array with an object and the method, for example: [object::class, 'method']
     * 
     * @return void
     */
    public function load_middleware(string $name, array $middleware)
    {
        $error = "Arguments to middleware with name: \"$name\" are invalid. Check that the class and the method exists and are valid.";

        Arrays::object_in_array($middleware, $error);

        // save the middleware
        $this->middlewares[$name] = $middleware;
    }

    /**
     * Loads a new parameter by type.
     * 
     * IMPORTANT: when you add a new parameter by type, it will be automatically prioritized.
     * Which means that it will be passed as a parameter with the first match. 
     * After that it will be removed.
     * 
     * @param string $type
     * 
     * The type of the parameter. It must be passed as a string.
     * 
     * @param mixed $value
     * 
     * The value of the parameter. 
     * If it is callable, the function will be called, and the return value of the function will be the value of the parameter. 
     * Example:
     *
     *       ->load_parameter_by_type("Example", function()
     *       {
     *           return "Test"
     *       })
     * 
     * @return void
     */
    public function load_parameter_by_type(string $type, mixed $value)
    {
        $this->params_by_type[$type] = $value;
    }

    /**
     * Loads every route.
     * 
     * @param array $server
     * 
     * The value of $server must be the same as $_SERVER
     * 
     * @return void
     */
    public function load(array $server)
    {
        // get url
        $request_uri = $server['REQUEST_URI'];

        // create a new RouteChecker
        $rc = new RouteChecker($request_uri, $this->routes);

        // set the method
        $method = $server['REQUEST_METHOD'];
        $rc->setMethod($method);

        // check if the url exists
        $url = $rc->url_exists();

        // unset global variables

        if($url)
        {
            $rc->setUrl($url);

            // check the request method

            if($rc->url_method_is_valid())
            {
                // get the params of the url
                $params = $rc->url_get_params();

                // check if the url has middlewares
                if($rc->url_has_middlewares())
                {
                    // set and validate middlewares
                    $rc->setValidMiddlewares($this->middlewares);

                    $are_valid = $rc->url_middlewares_are_valid();

                    if(is_bool($are_valid) && $are_valid == true)
                    {
                        // get the callable middlewares
                        $callable_middlewares = $rc->url_get_middlewares();

                        // call the middlewares
                        $_ = 0;

                        while($_ < count($callable_middlewares['objects']))
                        {
                            // create the object where the middleware is
                            $object = new $callable_middlewares['objects'][$_];

                            // call the function
                            $function_name = $callable_middlewares['methods'][$_];

                            Call::invoke([$object, $function_name], $params, $this->params_by_type);

                            $_++;
                        }
                    }
                    else
                    {
                        $rc->url_middlewares_not_valid_exception($are_valid);
                    }
                }

                $callable = $rc->url_get_callable();

                // call the action
                Call::invoke($callable, $params, $this->params_by_type);

            }
            else
            {
                unset($GLOBALS['routes_aliases']);
                unset($GLOBALS['routes']);
                $this->not_allowed_function();
                return;
            }
        }
        else
        {
            unset($GLOBALS['routes_aliases']);
            unset($GLOBALS['routes']);
            $this->not_found_function();
            return;
        }
    }
}

?>