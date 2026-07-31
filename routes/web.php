<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeveloperAccountController;
use App\Http\Controllers\ImageGenerationController;
use App\Http\Controllers\GabungController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\CarouselController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Jika sudah login, arahkan ke dashboard (/gabung)
    if (Auth::check()) {
        return redirect('/gabung');
    }
    // Jika belum login, arahkan ke form login
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

    // --- Views Dasar ---
    Route::view('/gabung', 'gabung')->name('gabung');
    Route::view('/edit', 'edit')->name('edit');
    Route::view('/carousel', 'carousel')->name('carousel');

    // --- Redirects ---
    Route::redirect('/korosel', '/carousel');
    Route::redirect('/Carousel', '/carousel');

    // --- Gemini Test & General Image Utils ---
    Route::get('/gemini-test', [ImageGenerationController::class, 'geminiTest'])
        ->name('gemini.test');
    Route::get('/download-image', [ImageGenerationController::class, 'downloadImage'])
        ->name('download.image');

    // --- Carousel ---
    Route::post('/carousel/process', [CarouselController::class, 'processStoryboard'])
        ->name('carousel.process');
    Route::get('/carousel/process-status/{id}', [CarouselController::class, 'checkProcessStatus'])
        ->name('carousel.process-status');
    Route::post('/carousel/render', [CarouselController::class, 'generate'])
        ->name('carousel.render');
    Route::get('/carousel/status/{ids}', [CarouselController::class, 'checkStatus'])
        ->name('carousel.status');

    // --- Gabung (Async Queue + Polling) ---
    Route::post('/gabung/generate', [GabungController::class, 'generate']);
    Route::get('/gabung/status/{ids}', [GabungController::class, 'checkStatus']);

    // --- Edit (Async Queue + Polling) ---
    Route::post('/edit/generate', [EditController::class, 'generate'])->name('edit.generate');
    Route::get('/edit/status/{ids}', [EditController::class, 'checkStatus'])->name('edit.status');

    // --- Product Artist ---
    Route::get('/artist', [ArtistController::class, 'index'])
        ->name('artist.index');
    Route::post('/artist/generate', [ArtistController::class, 'generate'])->name('artist.generate');

    Route::get('/artist/status/{prompt_id}', [ArtistController::class, 'checkStatus'])->name('artist.status');

    // --- Webhooks ---
    Route::post('/comfy-webhook', function (Request $request) {
        // Mencatat apa pun yang dikirim Comfy ke file log
        Log::info('Data masuk dari Comfy:', $request->all());

        if ($request->allFiles()) {
            Log::info('File yang dikirim:', $request->allFiles());
        }

        return response()->json(['message' => 'Berhasil ditangkap']);
    })->name('comfy.webhook');
});

/*
|--------------------------------------------------------------------------
| Fallback Route (404 Not Found)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});