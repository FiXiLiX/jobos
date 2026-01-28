<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MediaController;

Route::get('/', function () {
    return view('welcome');
});

// Protected routes that require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/expenses/{expense}/bill-picture', [ExpenseController::class, 'billPicture'])->name('expenses.bill-picture');
    Route::get('/media/{filename}', [MediaController::class, 'show'])->name('media.show');
});
