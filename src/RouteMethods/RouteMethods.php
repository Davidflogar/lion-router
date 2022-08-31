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

        // check if "$GLOBALS['routes']" exists
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

        // return a new instance of the class
        return new static($route);
    }
}

?>