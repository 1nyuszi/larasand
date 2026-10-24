<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\InfoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/info', [InfoController::class, 'index'])->name('info');
