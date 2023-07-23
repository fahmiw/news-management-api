<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CommentController;

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

Route::group(["prefix" => "auth"], function () {
    Route::post("login", [AuthController::class, "login"])->name('login');
    Route::post("register", [AuthController::class, "register"]);
    Route::group(["middleware" => ["auth:api"]], function() {
        Route::get("logout", [AuthController::class, "logout"]);
    });
});

Route::group(["prefix" => "news"], function () {
    Route::group(["prefix" => "comment"], function () {
        Route::group(["middleware" => ["auth:api"]], function() {
            Route::post("create", [CommentController::class, "create"]);
        });
    });

    Route::group(["middleware" => ["auth:api"]], function() {
        Route::get("all", [NewsController::class, "getAll"]);
        Route::get("detail/{id}", [NewsController::class, "detail"]);
    });

    Route::group(["middleware" => ["auth:api","admin"]], function() {
        Route::post("create", [NewsController::class, "create"]);
        Route::put("update/{id}", [NewsController::class, "update"]);
        Route::get("delete/{id}", [NewsController::class, "delete"]);
    });
});