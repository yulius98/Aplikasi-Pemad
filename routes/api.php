<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\ProdukController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::get('/v1/produk/search', [ProdukController::class, 'search']);
Route::get('/v1/produk/category', [ProdukController::class, 'category']);
Route::get('/v1/produk/rentangharga', [ProdukController::class, 'rentangharga']);


