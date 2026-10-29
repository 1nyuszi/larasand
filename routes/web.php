<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/info', [InfoController::class, 'index'])->name('info');

Route::get('/blog', BlogController::class)->name('blog');
Route::get('/blog/category/{category:slug}', BlogController::class)->name('category');
Route::get('/blog/tag/{tag:slug}', BlogController::class)->name('tag');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('post');
