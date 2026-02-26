<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.base');
});

Route::get('/user/detail', function () {
    return view('layouts.base');
});
