<?php

use App\Http\Controllers\HotelController;
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

Route::get('/', [HotelController::class, 'index'])->name('hotel.index');
Route::prefix('hotel')->group(function () {
    Route::get('/state', [HotelController::class, 'state'])->name('hotel.state');
    Route::post('/book', [HotelController::class, 'book'])->name('hotel.book');
    Route::post('/reset', [HotelController::class, 'reset'])->name('hotel.reset');
    Route::post('/random', [HotelController::class, 'random'])->name('hotel.random');
});
