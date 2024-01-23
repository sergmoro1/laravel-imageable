<?php

use Illuminate\Support\Facades\Route;
use Sergmoro1\Imageable\Http\Controllers\Api\ImageController;

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

Route::group(['middleware' => config('imageable.auth-method')], function () {
    Route::resources([
        'images' => ImageController::class,
    ]);
});
