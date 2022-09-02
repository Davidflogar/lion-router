<?php

namespace Lion\LionRouter\RouteChecker;

use Exception;

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
     */
    private $url;

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
            if(class_exists($action[0]) && method_exists($action[0], $action[1]))
            {
                $object = new $action[0];

                return $object->$action[1];
            }
            else
            {
                throw new Exception("Arguments to route with url: \"{$this->url}\" are invalid.");
            }
        }
        // on the other hand, it means that the action has been added as an anonymous function
        else
        {
            return $action;
        }
    }
}

?>