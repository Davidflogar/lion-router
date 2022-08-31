<?php

ini_set("display_errors", 1);

use Lion\LionRouter\Route;
use Lion\LionRouter\Router;

require_once __DIR__ . "/vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

require_once __DIR__ . "/routes.php";

$r = $GLOBALS['routes'];

$router = new Router($r,
function()
{
    echo "not found";
},
function()
{
    echo "no permitido";
});

$router->load($_SERVER);

unset($GLOBALS['routes']);

?>