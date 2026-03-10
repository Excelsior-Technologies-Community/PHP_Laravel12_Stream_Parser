<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreamParserController;

Route::get('/', [StreamParserController::class, 'index']);
Route::post('/upload', [StreamParserController::class, 'upload'])->name('upload');


// Route::get('/', function () {
//     return view('welcome');
// });
