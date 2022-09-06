<?php

namespace Lion\LionRouter\Group;

/**
 * This class has methods to work with groups of routes.
 */
class Group
{
    /**
     * Check if the current route is in a group.
     * 
     * @return bool
     */
    public static function in_route(): bool
    {
        return isset($GLOBALS['routes']['_group']) ? true : false;
    }

    /**
     * Returns the prefix of the route group.
     * 
     * @return string
     */
    public static function get_prefix(): string
    {
        return $GLOBALS['routes']['_group']['prefix'];
    }
}

?>