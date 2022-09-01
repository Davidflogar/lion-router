<?php

namespace Lion\LionRouter\RouteMethods;

use Lion\LionRouter\RouteStorer;

/**
 * This class has all the http methods supported by the "Lion" framework defined, such as post, put, delete, and get.
 */
class RouteMethods
{
    public static function get(string $url, array|callable $action)
    {
        // prepare the url
        if(!str_starts_with($url, "/"))
        {
            $url = "/" . $url;
        }

        // create the new route
        $route = [
            'action' => $action,
            'method' => 'GET'
        ];

        RouteMethods::global_routes_exists($url, $route);

        $route['url'] = $url;

        // return a new instance of the class
        return new static($route);
    }

    public static function post(string $url, array|callable $action)
    {
        // prepare the url
        if(!str_starts_with($url, "/"))
        {
            $url = "/" . $url;
        }

        // create the new route
        $route = [
            'action' => $action,
            'method' => 'POST'
        ];

        RouteMethods::global_routes_exists($url, $route);

        $route['url'] = $url;

        // return a new instance of the class
        return new static($route);
    }

    public static function put(string $url, array|callable $action)
    {
        // prepare the url
        if(!str_starts_with($url, "/"))
        {
            $url = "/" . $url;
        }

        // create the new route
        $route = [
            'action' => $action,
            'method' => 'PUT'
        ];

        RouteMethods::global_routes_exists($url, $route);

        $route['url'] = $url;

        // return a new instance of the class
        return new static($route);
    }

    public static function delete(string $url, array|callable $action)
    {
        // prepare the url
        if(!str_starts_with($url, "/"))
        {
            $url = "/" . $url;
        }

        // create the new route
        $route = [
            'action' => $action,
            'method' => 'DELETE'
        ];

        RouteMethods::global_routes_exists($url, $route);

        $route['url'] = $url;

        // return a new instance of the class
        return new static($route);
    }

    /**
     * Checks if "$GLOBALS['routes']" exists
     * 
     * @param string $url
     * @param array $route
     * 
     * @return void
     */
    private static function global_routes_exists(string $url, array $route)
    {
        if(!isset($GLOBALS['routes']))
        {
            $GLOBALS['routes'][$url] = $route;
        }
        else
        {
            // save the route
            RouteStorer::save($url, $route);
        }
    }
}

?>