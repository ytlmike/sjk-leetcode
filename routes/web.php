<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/ranking');
});

Route::get('/login', 'ViewController@login')->name('login');
Route::get('/register', 'ViewController@register')->name('register');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/my', function () {
        return view('my');
    })->name('my');

    Route::get('/ranking', function () {
        return view('ranking');
    })->name('ranking');

    Route::get('/logout', 'ViewController@logout')->name('logout');
});
