<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('static/{slug}', function (string $slug) {
    abort_unless(preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug), 404);
    return view("static.{$slug}");
})->where('slug', '[a-z0-9\-]+');
