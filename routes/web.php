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

Route::get('/', function () {
    return view('home');
})->middleware('already_installed');

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', 'InstallController@index')->name('index');
    Route::get('config', 'InstallController@config')->name('config');
    Route::post('run', 'InstallController@run')->name('run');
});

Auth::routes();
