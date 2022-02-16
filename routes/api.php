<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/health', function () {
    $message = '200 OK!';
    return response()->json([
        'message' => $message
    ], 200);
});

Route::resource('/recipe', RecipeController::class);
Route::resource('/menu', MenuController::class);
