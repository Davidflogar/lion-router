<?php

use Lion\LionRouter\Route;
use Lion\LionRouter\RouteStorer;

function route(string $name, ?array $data = null, bool $print = true)
{
    RouteStorer::route($name, $data, $print);
}

class A
{
    public function a()
    {
        route('example.route2', ['username' => 'user', 'id' => '1']);
    }
}

Route::get("/", [A::class, 'a'])->name('hola3')->middleware('hola', 'hola2');

Route::post("/", function(){
    echo "Hola 1";
})->name('route2');

Route::group(['name' => 'example.'], function(){

    Route::get('/si', function () { echo "Group"; });

    Route::get("/no/{username}/{id}", function(int $id, string $username){
        echo "Hola $username con id: $id";
    })->name('route2');

    Route::get('/no/other/si', function() { echo "a"; });

});

?>