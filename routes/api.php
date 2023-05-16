<?php

use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\ElasticSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Routs : crud
Route::controller(AuthController::class)->group(function(){
    Route::post('login','login')->name('login');
    Route::post('register','register')->name('register');
});

// book Routs : crud
Route::middleware('auth:sanctum')->controller(BookController::class)->group(function (){
    Route::post('add_book', 'addBook')->name('add.book');
    Route::put('edit_book', 'editBook')->name('edit.book');
    Route::delete('delete_book', 'deleteBook')->name('delete.book');

    Route::post('sign-out', [AuthController::class, 'signOut'])->name('logout');
});

Route::prefix('elasticsearch')->controller(ElasticSearchController::class)->group(function (){
    Route::get('books','getBooks')->name('elasticsearch.books');
});
