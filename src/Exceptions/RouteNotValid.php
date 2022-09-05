<?php

namespace Lion\LionRouter\Exceptions;

use Exception;

class RouteNotValid extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

?>