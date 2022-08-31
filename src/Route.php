<?php

namespace Lion\LionRouter;

use Lion\LionRouter\RouteMethods\RouteMethods;

/**
 * Represents a unique route.
 */
class Route extends RouteMethods
{
    /**
     * The current route.
     * 
     * @var array
     */
    private $route;


    public function __construct(array $data = [])
    {
        $this->route = $data;
    }

    /**
     * Saves the route.
     * 
     * @return void
     */
    private function save()
    {
        // save the route
        RouteStorer::save($this->route['url'], $this->route);
    }

    /**
     * Adds one or multiple middlewares.
     * 
     * @return self
     */
    public function middleware(string ...$middlewares): self
    {
        $this->route['middlewares'] = $middlewares;

        $this->save();

        return $this;
    }

    /**
     * Adds a name to the route.
     * 
     * @param string $name
     * 
     * @return self
     */
    public function name(string $name): self
    {
        $this->route['name'] = $name;

        $this->save();

        return $this;
    }
}

?>