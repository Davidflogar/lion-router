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

        // check if the route exists
        if($search == false)
        {
            $GLOBALS['routes'][$key] = $route;
        }
        // if it exists we compare the differences and adds them
        else
        {
            // this is used when adding new middleware or a name to a route,
            // so any change to a route is automatically saved

            // get a copy of the 'action' before deleting it
            $action_from_route = $route['action'];

            // delete old actions
            unset($routes[$key]['action']);
            unset($route['action']);

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

    /**
     * Check if the provided string matches any URL.
     * 
     * @param string $string
     * 
     * @param array $routes
     * 
     * @return bool
     */
    public static function matches(string $string, array $routes): bool|string
    {
        // this variable has "false" as the value if the URL was not found; otherwise the value will be equal to $string.
        $found = false;

        foreach ($routes as $route) {
            // prepare the url

            // if $url is not equal to null, it means the url needs parameters
            $url = preg_filter("/[\{.*\}]{1,}/", "$0", $route);

            if($url != null)
            {
                // convert the url to a regex
                $old_url = preg_quote($url, "/");
                $new_url = preg_replace("/(\\\{.*\}){1,}/U", "(.*)", $old_url);

                // check if matches
                $matches = preg_match("($new_url)", $string);

                if($matches)
                {
                    $found = $url;

                    return $found;
                }
            }
            // otherwise it means the url is a static url which means the url will be matched directly
            else
            {
                if($string == $route)
                {
                    $found = $route;

                    return $found;
                }
            }
        }

        return $found;
    }
}

?>