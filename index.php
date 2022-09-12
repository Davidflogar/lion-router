<?php

ini_set("display_errors", 1);

use Lion\LionRouter\Router;

require_once __DIR__ . "/vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

class Example
{
    public function not_allowed()
    {
        echo "No permitido.";
    }

    public function not_found()
    {
        echo "No encontrado.";
    }

    public function hola()
    {
        echo "Middleware 1 <br>";
    }

    public function hola2()
    {
        echo "Middleware 2 <br>";
    }
}

require_once __DIR__ . "/routes.php";

$r = $GLOBALS['routes_aliases'];

$router = new Router($r, [Example::class, 'not_found'], [Example::class, 'not_allowed']);

$router->load_middleware('hola', [Example::class, 'hola']);

$router->load_middleware('hola2', [Example::class, 'hola2']);

$router->load_parameter_by_type("string", function() { return "siu"; } );

$router->load($_SERVER);

?>