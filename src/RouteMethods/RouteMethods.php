<?php

namespace Lion\LionRouter\RouteMethods;

use Lion\LionRouter\RouteStorer;

/**
 * This class has all the http methods supported by the "Lion" framework defined, such as post, put, delete, and get.
 */
class RouteMethods
{
    public static function get(string $url, array|callable $action): static
    {
        // process the route
        $route = RouteMethods::body($url, $action, "GET");

        // return a new instance
        return new static($route);
    }

    public static function post(string $url, array|callable $action): static
    {
        // process the route
        $route = RouteMethods::body($url, $action, "POST");

        // return a new instance
        return new static($route);
    }

    public static function put(string $url, array|callable $action): static
    {
        // process the route
        $route = RouteMethods::body($url, $action, "PUT");

        // return a new instance
        return new static($route);
    }

    public static function delete(string $url, array|callable $action): static
    {
        // process the route
        $route = RouteMethods::body($url, $action, "DELETE");

        // return a new instance
        return new static($route);
    }

    /**
     * This is the main function for, get, post, put and delete methods.
     * 
     * @param string $url
     * @param array $route
     * 
     * @return array
     */
    private static function body(string $url, array|callable $action, string $method): array
    {
        // prepare the url
        if(!str_starts_with($url, "/"))
        {
            $url = "/" . $url;
        }

        // create the new route
        $route = [
            'action' => $action,
            'method' => $method
        ];

        // checks if "$GLOBALS['routes']" exists
        if(!isset($GLOBALS['routes']))
        {
            $GLOBALS['routes'][$url] = $route;
        }
        else
        {
            // save the route
            RouteStorer::save($url, $route);
        }

        $route['url'] = $url;

        // return the route
        return $route;
    }
}

?>