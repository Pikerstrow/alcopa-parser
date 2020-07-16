<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/parser/start', 'Parser\ParserController@startParser')->name('start_parser');
Route::post('/parser/get-progress', 'Parser\ParserController@getParserProgress')->name('parser_progress');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
