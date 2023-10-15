<?php

use App\Http\Controllers\InstaController;
use App\Http\Controllers\ProfileController;
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

Route::get('/', [InstaController::class, 'home'])
->name('insta_home');

Route::get('/user/{name}', [InstaController::class, 'user'])
->name('insta_user');

// could be moved to /api/
Route::get('/insta_cron/{id}/{md5}', [InstaController::class, 'cron'])
->name('insta_cron');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', [InstaController::class, 'dashboard'])
    ->name('dashboard');
    // keep breeze dashboard

    Route::post('/insta_api', [InstaController::class, 'api'])
    ->name('insta_api');

    Route::any('/insta_refresh_access_token', [InstaController::class, 'refresh_access_token'])
    ->name('insta_refresh_access_token');

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
