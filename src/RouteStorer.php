<?php

namespace Lion\LionRouter;

/**
 * This class has the necessary methods to save and get routes and middlewares in the proper way.
 */
class RouteStorer
{
    /**
     * Saves a new route.
     * 
     * @param string $key
     * @param array $route
     * 
     * @return void
     */
    public static function save(string $key, array $route)
    {
        // get all the routes
        $routes = $GLOBALS['routes'];

        $search = array_key_exists($key, $routes);

        if($search == false)
        {
            $GLOBALS['routes'][$key] = $route;
        }
        else
        {
            // get a copy of the 'action' before deleting it
            $action_from_route = $route['action'];

            // delete old actions
            unset($routes[$key]['action']);
            unset($route['action']);

            var_dump($routes[$key]);
            die;
            // get the diff
            $diff = array_diff_key($route, $routes[$key]);

            // set the action to the diff
            $diff['action'] = $action_from_route;

            // save the changes
            $key_diff = array_diff_key($diff, $routes[$key]);

            $merge = array_merge($key_diff, $routes[$key]);

            $GLOBALS['routes'][$key] = $merge;
        }
    }

    /**
     * Gets a route by its name. Or returns 404 if it not exists.
     * 
     * @param string $url
     * 
     * @return void
     */
    public static function get_route_by_name(string $name): mixed
    {
        $column = array_column($GLOBALS['routes'], 'name', 'url');

        $search = array_search($name, $column);

        if($search == false)
        {
            return 404;
        }
        else
        {
            return $search;
        }
    }
}

?>