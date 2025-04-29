<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('posts/fetch', [PostController::class, 'fetchAndStore']);
Route::get('posts', [PostController::class, 'index']);
