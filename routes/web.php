<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('welcome');
})->name('dashboard');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::post('/logout', function () {
    return redirect('/');
})->name('logout');

if (app()->environment('local')) {
    Route::get('/_design', function () {
        return view('design-system');
    });
}
