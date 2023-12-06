<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/products/all', [App\Http\Controllers\ProductController::class, 'list'])->name('product.all');
Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('product.new');
Route::post('/products/create', [App\Http\Controllers\ProductController::class, 'store'])->name('product.store');

Route::get('/blogs/{id}', [App\Http\Controllers\BlogController::class, 'index']);
Route::post('/blogs/update/{id}', [App\Http\Controllers\ProductController::class, 'update']);
Route::delete('/blogs/delete/{id}', [App\Http\Controllers\ProductController::class, 'delete']);
