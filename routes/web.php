<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptionController;
use App\Http\Controllers\DeveloperAccountController;
use App\Http\Controllers\ImageGenerationController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\StoryboardController;
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

    Route::post('/gabung/generate', [ImageGenerationController::class, 'generateGabung'])
        ->name('gabung.generate');

    Route::post('/edit/generate', [ImageGenerationController::class, 'generateEdit'])
        ->name('edit.generate');

    Route::post('/artist/generate', [ImageGenerationController::class, 'generateArtist'])
        ->name('artist.generate');

    Route::post('/carousel/render', [ImageGenerationController::class, 'renderCarousel'])
        ->name('carousel.render');

    /*
    |--------------------------------------------------------------------------
    | Caption (BARU)
    |--------------------------------------------------------------------------
    | Sebelumnya CaptionController & CaptionService sudah dibuat lengkap
    | tapi belum pernah didaftarkan route-nya, jadi fitur ini tidak bisa
    | diakses sama sekali dari UI. Ditambahkan supaya sesuai modul Minggu 1
    | (generate caption) & Minggu 3-4 (form input + output teks).
    */
    Route::view('/caption', 'Caption')->name('caption');

    Route::post('/caption/generate', [CaptionController::class, 'generate'])
        ->name('caption.generate');

    /*
    |--------------------------------------------------------------------------
    | Storyboard / Ide Video (BARU)
    |--------------------------------------------------------------------------
    | Sama seperti Caption: StoryboardController & StoryboardService sudah
    | siap, tapi belum ada route + view. Fitur ini yang memenuhi requirement
    | modul Minggu 4 no.1: "Memilih format output: ... ide video/storyboard".
    | Output teks storyboard (Scene/Visual/Camera/Mood) di sini bisa lanjut
    | diexport jadi PDF lalu di-upload ke fitur Carousel (renderCarousel)
    | untuk dijadikan gambar per-scene.
    */
    Route::view('/storyboard', 'Storyboard')->name('storyboard');

    Route::post('/storyboard/generate', [StoryboardController::class, 'generate'])
        ->name('storyboard.generate');

    Route::get('/prompts', [PromptController::class, 'page'])
        ->name('prompts.page');

    Route::get('/api/prompts', [PromptController::class, 'index'])
        ->name('prompts.index');

    Route::post('/api/prompts', [PromptController::class, 'store'])
        ->name('prompts.store');

    Route::put('/api/prompts/{id}', [PromptController::class, 'update'])
        ->name('prompts.update');

    Route::delete('/api/prompts/{id}', [PromptController::class, 'destroy'])
        ->name('prompts.destroy');

    Route::post('/api/prompts/{id}/use', [PromptController::class, 'markUsed'])
        ->name('prompts.use');

    Route::post('/carousel/analyze', [ImageGenerationController::class, 'analyzeStoryboard'])
        ->name('carousel.analyze');    
});