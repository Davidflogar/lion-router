<?php

use Lion\LionRouter\Route;
use Lion\LionRouter\RouteStorer;

function route(string $name)
{
    $search = RouteStorer::get_route_by_name($name);

    if($search == 404)
    {
        throw new Exception("Could not find route \"$name\".");
        return;
    }

    echo $search;
}

Route::get("/", function(){
    echo "Hola 1";
})->middleware('hola', 'hola2')->name('hola3');

Route::get("/hola", function(){
    echo "Hola 1";
});

Route::get("/hola2", function(){
    echo "Hola 2";
})->name('route2');

?>