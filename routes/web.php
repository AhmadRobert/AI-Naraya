<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeveloperAccountController;
use App\Http\Controllers\ImageGenerationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::view('/beli', 'beli')->name('beli');

/*
|--------------------------------------------------------------------------
| Developer Create Account
|--------------------------------------------------------------------------
| Buka: http://127.0.0.1:8000/buat-akun
| Kode developer diambil dari .env: DEV_CREATE_KEY
*/

Route::get('/buat-akun', [DeveloperAccountController::class, 'create'])
    ->name('developer.account.form');

Route::post('/buat-akun', [DeveloperAccountController::class, 'store'])
    ->name('developer.account.store');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::view('/gabung', 'gabung')->name('gabung');
    Route::view('/edit', 'edit')->name('edit');
    Route::view('/artist', 'artist')->name('artist');
    Route::view('/carousel', 'carousel')->name('carousel');

    Route::redirect('/korosel', '/carousel');
    Route::redirect('/Carousel', '/carousel');

    Route::get('/gemini-test', [ImageGenerationController::class, 'geminiTest'])
        ->name('gemini.test');

    Route::post('/gabung/generate', [ImageGenerationController::class, 'generateGabung'])
        ->name('gabung.generate');

    Route::post('/edit/generate', [ImageGenerationController::class, 'generateEdit'])
        ->name('edit.generate');

    Route::post('/artist/generate', [ImageGenerationController::class, 'generateArtist'])
        ->name('artist.generate');

    Route::post('/carousel/render', [ImageGenerationController::class, 'renderCarousel'])
        ->name('carousel.render');

    Route::post('/korosel/render', [ImageGenerationController::class, 'renderKorosel'])
        ->name('korosel.render');
});
