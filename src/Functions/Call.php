<?php

namespace Lion\LionRouter\Functions;

use DomainException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * This class is used to call a function with its parameters without knowing the actual parameters.
 */
class Call
{
    public static function invoke(array|callable $action, array $params)
    {
        // creates the reflection object

        if(is_callable($action) && !is_array($action))
        {
            $rf = new ReflectionFunction($action);
        }
        else if(is_array($action))
        {
            $rf = new ReflectionMethod(new $action[0], $action[1]);
        }

        $real_params = $rf->getParameters();

        $valid_params = [];

        foreach($real_params as $arg)
        {
            if(!array_key_exists($arg->getName(), $params) && !$arg->isOptional())
            {
                throw new DomainException('Cannot resolve the parameter "' . $arg->getName() . "\".");
            }

            if(!array_key_exists($arg->getName(), $params)) {
                continue;
            }

            $valid_params[$arg->getName()] = $params[$arg->getName()];
        }

        // call the function
        $rf->invoke(...$valid_params);
    }
}

?>