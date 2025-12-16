<?php

use App\Http\Controllers\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('user', UserController::class)->withoutMiddleware([VerifyCsrfToken::class]);

// Route::get('/test-form', function () {
//     return csrf_token();
// });
