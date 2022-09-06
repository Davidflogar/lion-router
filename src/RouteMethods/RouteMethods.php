<?php

namespace Lion\LionRouter\RouteMethods;

use Exception;
use Lion\LionRouter\Group\Group;
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

    public static function group(array $options, callable $action): void
    {
        // set the options required
        $required = ['prefix'];

        foreach($required as $key)
        {
            if(!key_exists($key, $options))
            {
                throw new Exception("Cannot create route group, requires key: \"$key\".");
            }
        }

        // set the prefix and create prefix
        $GLOBALS['routes']['_group']['prefix'] = $options['prefix'];

        // call the action
        $action();

        // unset the group
        unset($GLOBALS['routes']['_group']);
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
        // this is used when the routes are inside a route group
        if(Group::in_route())
        {
            $url = Group::get_prefix() . $url;
        }

        // prepare the url
        if(!str_starts_with($url, "/"))
        {
            $url = "/" . $url;
        }

        // create the new route
        $route = [
            'action' => $action,
            'method' => $method,
            'url' => $url,
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

        // return the route
        return $route;
    }
}

?>