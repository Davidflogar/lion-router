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
    public static function invoke(array|callable $action, array $params, array $valid_by_type)
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

            // check the param by type
            $type = $arg->getType()->getName();

            if(key_exists($type, $valid_by_type))
            {
                // get the value of the type
                $value = $valid_by_type[$type];

                // check if is callable
                if(is_callable($value))
                {
                    $value = $value();
                }

                $valid_params[$arg->getName()] = $value;
                continue;
            }

            if(!array_key_exists($arg->getName(), $params)) {
                continue;
            }

            // add the param
            $valid_params[$arg->getName()] = $params[$arg->getName()];
        }

        // call the function
        if(count($valid_params) != 0)
        {
            $rf->invoke(...$valid_params);
        }
        else
        {
            if(is_callable($action) && !is_array($action))
            {
                $rf->invoke();
            }
            else if(is_array($action))
            {
                call_user_func([new $action[0], $action[1]], new $action[0]);
            }
        }
    }
}

?>