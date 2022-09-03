<?php

namespace Lion\LionRouter\RouteChecker;

use Exception;
use Lion\LionRouter\Helpers\Arrays;

/**
 * This class has methods to access data from a url by the url.
 */
class RouteChecker
{
    /**
     * The list where all the routes will be.
     * 
     * @var array
     */
    private $routes;

    /**
     * The url with which the object will be working.
     * 
     * @var string
     */
    private $url;

    /**
     * The list of valid middlewares.
     * 
     * @var array
     */
    private $valid_middlewares;

    /**
     * Set the valid middlewares.
     * 
     * @param array $list
     * 
     * The list of valid middlewares.
     * 
     * @return void
     */
    public function setValidMiddlewares(array $list): void
    {
        $this->valid_middlewares = $list;
    }

    /**
     * Constructor
     * 
     * @param string $url
     * 
     * The url with which the object will be working.
     * 
     * @param array $list
     * 
     * The list where all the routes will be.
     * 
     * @return void
     */
    public function __construct(string $url, array $list)
    {
        $this->url = $url;

        $this->routes = $list;
    }

    /**
     * Returns the data of the url, such as the action or the middlewares.
     * 
     * The URL must exist to call this function, otherwise the function will throw an error.
     * 
     * 
     * @return array
     */
    public function get_url_data(): array
    {
        return [$this->routes[$this->url]];
    }

    /**
     * Checks if the url exists.
     * 
     * 
     * @return bool
     */
    public function url_exists(): bool
    {
        return isset($this->routes[$this->url]) ? true : false;
    }

    /**
     * Checks if the url has middlewares.
     * 
     * @return bool
     */
    public function url_has_middlewares(): bool
    {
        return isset($this->routes[$this->url]['middlewares']) ? true : false;
    }

    /**
     * Returns the middlewares of the url.
     * 
     * @return array
     */
    public function url_get_middlewares(): array
    {
        $objects = [];
        $methods = [];

        foreach($this->valid_middlewares as $middleware)
        {
            array_push($objects, $middleware[0]);
            array_push($methods, $middleware[1]);
        }

        return [
            'objects' => $objects,
            'methods' => $methods
        ];
    }

    /**
     * Validates the middlewares of the url based on the list passed as argument.
     * 
     * On error it returns the name of the middleware.
     * 
     * @return bool
     */
    public function url_middlewares_are_valid(): string|bool
    {
        // iterate over the middlewares of the url
        foreach($this->routes[$this->url]['middlewares'] as $middleware)
        {
            // check if the middleware exists
            $exists = key_exists($middleware, $this->valid_middlewares);

            if($exists == false)
            {
                return $middleware;
            }
        }

        return true;
    }

    /**
     * Returns the http method for the route.
     * 
     * @return string
     */
    public function url_get_method(): string
    {
        return $this->routes[$this->url]['method'];
    }

    /**
     * Returns the action of the route.
     *
     * @return callable
     */
    public function url_get_callable(): callable
    {
        // get the action
        $action = $this->routes[$this->url]['action'];

        // check if the action has been added as "array(object, action)"
        if(is_array($action))
        {
            // validate and return the action
            $error = "Arguments to route with url: \"{$this->url}\" are invalid. Check that the class and the method exists and are valid.";

            Arrays::object_in_array($action, $error);

            $object = new $action[0];

            return $object->$action[1];
        }
        // on the other hand, it means that the action has been added as an anonymous function
        else if(is_callable($action))
        {
            return $action;
        }
    }

    /**
     * Throws an exception in case the middlewares are not valid.
     * 
     * @param string $middleware
     * 
     * The middleware.
     * 
     * @return void
     */
    public function url_middlewares_not_valid_exception(string $middleware): void
    {
        throw new Exception("The middleware \"$middleware\" is not valid, check that the middleware exists and is registered.");
    }
}

?>