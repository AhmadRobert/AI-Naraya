<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptionController;
use App\Http\Controllers\DeveloperAccountController;
use App\Http\Controllers\ImageGenerationController;
use App\Http\Controllers\GabungController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\ArtistController;
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

    /*
    |------------------------------------------------------------------
    | Views
    |------------------------------------------------------------------
    */
    Route::view('/gabung', 'gabung')->name('gabung');
    Route::view('/edit', 'edit')->name('edit');
    Route::get('/artist', [ArtistController::class, 'index'])->name('artist');
    Route::view('/carousel', 'carousel')->name('carousel');

    Route::redirect('/korosel', '/carousel');
    Route::redirect('/Carousel', '/carousel');

    /*
    |------------------------------------------------------------------
    | Gabungkan Foto -> ComfyUI (implementasi Candra)
    |------------------------------------------------------------------
    */
    Route::post('/gabung/generate', [GabungController::class, 'generate'])
        ->name('gabung.generate');

    Route::get('/gabung/status/{ids}', [GabungController::class, 'checkStatus'])
        ->name('gabung.status');

    /*
    |------------------------------------------------------------------
    | Edit Foto -> ComfyUI (implementasi Candra)
    |------------------------------------------------------------------
    */
    Route::post('/edit/generate', [EditController::class, 'generate'])
        ->name('edit.generate');

    Route::get('/edit/status/{ids}', [EditController::class, 'checkStatus'])
        ->name('edit.status');

    /*
    |------------------------------------------------------------------
    | Produk Artist -> Grok (implementasi Oltha)
    |------------------------------------------------------------------
    */
    Route::post('/artist/generate', [ImageGenerationController::class, 'generateArtist'])
        ->name('artist.generate');

    /*
    |------------------------------------------------------------------
    | Carousel -> Grok (implementasi Oltha)
    |------------------------------------------------------------------
    */
    Route::post('/carousel/render', [ImageGenerationController::class, 'renderCarousel'])
        ->name('carousel.render');

    Route::get('/download-image', [ImageGenerationController::class, 'downloadImage'])
        ->name('download.image');

    /*
    |------------------------------------------------------------------
    | Caption -> ComfyUI (implementasi Candra)
    |------------------------------------------------------------------
    */
    Route::view('/caption', 'Caption')->name('caption');

    Route::post('/caption/generate', [CaptionController::class, 'generate'])
        ->name('caption.generate');

    Route::get('/caption/status/{promptId}', [CaptionController::class, 'checkStatus'])
        ->name('caption.status');

    /*
    |------------------------------------------------------------------
    | Storyboard -> Grok (implementasi Oltha)
    |------------------------------------------------------------------
    */
    Route::view('/storyboard', 'Storyboard')->name('storyboard');

    Route::post('/storyboard/generate', [StoryboardController::class, 'generate'])
        ->name('storyboard.generate');

    /*
    |------------------------------------------------------------------
    | Prompt Library
    |------------------------------------------------------------------
    */
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
});