<?php

namespace Lion\LionRouter\Helpers;

use Exception;

/**
 * This class has methods to validate arrays.
 */
class Arrays
{
    /**
     * Validates an array passed as [object::class, 'method'] and checks that the object and method exist and are valid.
     * 
     * @param array $array
     * 
     * The array
     * 
     * @param string $error
     * 
     * The error message that is displayed when the array is invalid.
     * 
     * @return bool
     */
    public static function object_in_array(array $array, string $error): bool
    {
        if(class_exists($array[0]) && method_exists($array[0], $array[1]))
        {
            return true;
        }
        else
        {
            throw new Exception($error);
        }
    }
}


?>