<?php

namespace Lion\LionRouter;

use Exception;

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
     * @return int|string
     */
    public static function get_route_by_name(string $name): int|string
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
     * Checks if the provided string matches any URL.
     * 
     * @param string $string
     * 
     * @param array $routes
     * 
     * @return bool
     */
    public static function matches(string $string, array $routes): bool|string
    {
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
                    return $route;
                }
            }
            // otherwise it means the url is a static url which means the url will be matched directly
            else
            {
                if($string == $route)
                {
                    return $string;
                }
            }
        }

        return false;
    }

    /**
     * Returns the parameters of a route.
     * 
     * @param string $route
     * 
     * @return bool|array
     */
    public static function get_url_params(string $route): bool|array
    {
        // if $url is not equal to null, it means the url needs parameters
        $url = preg_filter("/[\{.*\}]{1,}/", "$0", $route);

        if($url != null)
        {
            // check if matches
            $matches = [];

            preg_match_all("/(\{.*\}){1,}/U", $url, $matches);

            if(count($matches) >= 1)
            {
                /**
                 * Here the idea is to make $params have the url parameters in the "params" index.
                 * And in the "url" index the complete url, but where the parameters would go, the result of the "time" function will go. 
                 * For later use the result of the time function to identify where the parameters should go.
                 * Example:
                 * 
                 * // example url: /user/{username}/profile
                 * 
                 * $time = time();
                 * 
                 * $params = [
                 *      'params' => ['username'],
                 *      'url' => ['/user/', $time, '/profile'],
                 *      'token' => [$time],
                 * ]
                 * 
                 */

                $params = [];
                $params['params'] = str_replace(["{", "}"], "", $matches[0]);

                $time = time();

                $url_splited = preg_replace("/(\{.*\}){1,}/U", $time, $url);

                $new_time = $time + 10;

                foreach($params['params'] as $param)
                {
                    $url_splited = preg_replace("($time)", "$param$new_time", $url_splited, 1);
                }

                $params['url'] = $url_splited;

                $params['token'] = $new_time;

                return $params;
            }
        }

        // otherwise it means the url is a static url which means the url does not need any parameters
        return false;
    }

    /**
     * route function.
     */
    public static function route(string $name, ?array $data = null, bool $print = true)
    {
        $search = RouteStorer::get_route_by_name($name);
    
        if($search == 404)
        {
            throw new Exception("Could not find route \"$name\".");
            return;
        }
    
        if(is_array($data))
        {
            $params = RouteStorer::get_url_params($search);
    
            $token = $params['token'];
            $search = $params['url'];
    
            foreach($params['params'] as $param)
            {
                // validate the key to generate the url
                if(key_exists($param, $data) && is_string($data[$param]))
                {
                    $search = str_replace("$param$token", $data[$param], $search);
                }
                else
                {
                    $keys = implode(", ", $params['params']);
    
                    throw new Exception("The keys to generate the route \"$name\" are not valid. Requires \"[$keys]\" and must be passed as a string.");
                }
            }
    
        }
    
        if($print)
        {
            echo $search;
        }
        else
        {
            return $search;
        }
    }

}

?>