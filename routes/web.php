<?php

use Illuminate\Support\Facades\Route;

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
/**
 * IMPORTANT!!! Route and controller for developing mode only (better debugging)!
 * For development use command (php artisan parser:parse_alcopa)
 */

Route::group(['prefix' => 'parser'], static function () {
    Route::get("parse", 'Parser\ParserController@parse');
});


Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
