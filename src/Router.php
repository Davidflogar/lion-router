<?php

namespace Lion\LionRouter;

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

        // check if the url exists
        if($rc->url_exists())
        {
            if($rc->url_has_middlewares())
            {

            }

            // check the request method
            $method = $server['REQUEST_METHOD'];

            if($method == $rc->url_get_method())
            {
                $callable = $rc->url_get_callable();

                // call the action
                $result =  $callable();

                if($result != null && (is_string($result) || is_int($result)))
                {
                    echo $result;
                }
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