<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Auth::routes();
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
