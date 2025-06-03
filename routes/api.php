<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("signup", [AuthController::class, 'signup']);
Route::post("login", [AuthController::class, 'login']);

//User can't directly logout without being logged in ... so here we pass a middleware
Route::post("logout", [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
    // This will generate the following routes:

    // Method	        URI	Action	        Route       Name
    // GET	            /posts	            index	    posts.index
    // POST	            /posts	            store	    posts.store
    // GET	            /posts/{post}	    show	    posts.show
    // PUT	            /posts/{post}	    update	    posts.update
    // DELETE	        /posts/{post}	    destroy	    posts.destroy


});
