<?php

namespace Lion\LionRouter;

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
     */
    private $config;

    /**
     * Calls the "not_found" function
     */
    public function not_found_function()
    {
        // call the function
        $this->config["not_found"]();

        // set the header and status code
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        http_response_code(404);
    }

    /**
     * Calls the "not allowed" function
     */
    public function not_allowed_function()
    {
        // call the function
        $this->config["not_allowed"]();

        // set the header and status code
        header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed");
        http_response_code(405);
    }

    /**
     * Constructor.
     */
    public function __construct(array $array, callable $not_found, callable $not_allowed)
    {
        // set all the routes
        $this->routes = $array;

        // set the url not found function
        $this->config['not_found'] = $not_found;

        // set the method not allowed function
        $this->config['not_allowed'] = $not_allowed;
    }

    /**
     * Loads every route.
     * 
     * @param array $request
     * 
     * @return void
     */
    public function load(array $server)
    {
        $request_uri = $server['REQUEST_URI'];

        // check if the url exists
        if(key_exists($request_uri, $this->routes))
        {
            // check the request method
            $method = $server['REQUEST_METHOD'];

            if($method == $this->routes[$request_uri]['method'])
            {
                $this->routes[$request_uri]['action']();
            }
            else
            {
                $this->not_allowed_function();
                return;
            }
        }
        else
        {
            $this->not_found_function();
            return;
        }
    }
}

?>