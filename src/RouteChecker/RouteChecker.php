<?php

namespace Lion\LionRouter\RouteChecker;

use Closure;
use Exception;
use Lion\LionRouter\Exceptions\RouteNotValid;
use Lion\LionRouter\Helpers\Arrays;
use Lion\LionRouter\RouteStorer;

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
     * The uri of the request.
     * 
     * @var string
     */
    private $uri;

    /**
     * The list of valid middlewares.
     * 
     * @var array
     */
    private $valid_middlewares;

    /**
     * The http method of the request.
     * 
     * @var string
     */
    private $request_method;

    /**
     * The regular expression to validate the url.
     * 
     * @var string
     */
    private $regex;

    /**
     * Sets the http method of the request.
     * 
     * @param string $method
     * 
     * @return void
     */
    public function setMethod(string $method)
    {
        $this->request_method = $method;
    }

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
     * Sets the url with which the object will be working.
     * 
     * @param string $url
     * 
     * The url.
     * 
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url . "_" . $this->request_method;
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
        $this->uri = $url;

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
     * Checks if the url exists. If it exists it returns the array key.
     * 
     * @return bool
     */
    public function url_exists(): bool|string
    {
        // check if the url matches with any url
        $urls = array_column($this->routes, 'url');

        $matches = RouteStorer::matches($this->url, $urls, true);

        if(is_array($matches))
        {
            $this->regex = isset($matches[1]) ? $matches[1] : "";

            return $matches[0];
        }
        else
        {
            return $matches;
        }

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
     * Validates the http method for the route.
     * 
     * @return bool
     */
    public function url_method_is_valid(): bool
    {
        return isset($this->routes[$this->url]) ? true : false;
    }

    /**
     * Returns the action of the route.
     *
     * @return callable
     */
    public function url_get_callable(): callable|array
    {
        // get the action
        $action = $this->routes[$this->url]['action'];

        // check if the action has been added as "array(object, action)"
        if(is_array($action))
        {
            // validate and return the action
            $error = "Arguments to route with url: \"{$this->url}\" are invalid. Check that the class and the method exists and are valid.";

            Arrays::object_in_array($action, $error);

            return $action;
        }
        // on the other hand, it means that the action has been added as an anonymous function
        else if(is_callable($action))
        {
            return $action;
        }
    }

    /**
     * Returns the params of the url.
     * 
     * @return mixed
     */
    public function url_get_params(): mixed
    {
        $matches = [];

        // check if the current url needs parameters
        if($this->regex != "")
        {
            // get the value of the params
            preg_match("/{$this->regex}/", $this->uri, $matches, PREG_UNMATCHED_AS_NULL);

            unset($matches[0]);

        }

        // get the name of the params
        $params = RouteStorer::get_url_params($this->routes[$this->url]['url'], true);
        $url_params = isset($params['params']) ? $params['params'] : [];

        $final_params = [];

        for($counter = 0; $counter < count($url_params); $counter++)
        {
            $final_params[$url_params[$counter]] = $matches[$counter+1];
        }

        return $final_params;
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
        $e = new RouteNotValid("The middleware \"$middleware\" is not valid, check that the middleware exists and is registered.");

        throw $e;
    }
}

?>