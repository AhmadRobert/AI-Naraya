<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptionController;
use App\Http\Controllers\DeveloperAccountController;
use App\Http\Controllers\ImageGenerationController;
use App\Http\Controllers\GabungController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\StoryboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'super_admin') {
            return redirect()->route('admin.super.index');
        } elseif ($role === 'admin_umkm') {
            return redirect()->route('admin.company.index');
        }
        return redirect()->route('gabung');
    }
    return redirect()->route('login');
});

Route::view('/beli', 'beli')->name('beli');

/*
|--------------------------------------------------------------------------
| Developer Create Account
|--------------------------------------------------------------------------
*/

Route::middleware('developer.key')->group(function () {
    Route::get('/buat-akun', [DeveloperAccountController::class, 'create'])
        ->name('developer.account.form');

    Route::post('/buat-akun', [DeveloperAccountController::class, 'store'])
        ->name('developer.account.store');
});

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

Route::middleware(['auth', \App\Http\Middleware\CheckCompanyActive::class])->group(function () {

    /*
    |------------------------------------------------------------------
    | Profile Routes
    |------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /*
    |------------------------------------------------------------------
    | AI Feature Routes (Only for 'user' role)
    |------------------------------------------------------------------
    */
    Route::middleware([\App\Http\Middleware\UserMiddleware::class])->group(function () {
        /*
        |------------------------------------------------------------------
        | Views
        |------------------------------------------------------------------
        | FIX: /edit sebelumnya pakai view('edit') huruf kecil, padahal file
        | aslinya Edit.blade.php (huruf besar E) - akan gagal "View not
        | found" di server Linux (case-sensitive). Disamakan jadi 'Edit'.
        */
        Route::view('/gabung', 'gabung')->name('gabung');
        Route::view('/edit', 'Edit')->name('edit');
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

    /*
    |------------------------------------------------------------------
    | Admin Dashboards
    |------------------------------------------------------------------
    */
    Route::middleware([\App\Http\Middleware\SuperAdminMiddleware::class])->group(function () {
        Route::get('/super-admin', [\App\Http\Controllers\SuperAdminReportController::class, 'index'])
            ->name('admin.super.index');
        
        // Company Management Routes
        Route::post('/super-admin/companies', [\App\Http\Controllers\SuperAdminReportController::class, 'storeCompany'])
            ->name('admin.super.companies.store');
        Route::put('/super-admin/companies/{company}/markup', [\App\Http\Controllers\SuperAdminReportController::class, 'updateMarkup'])
            ->name('admin.super.companies.markup');
        Route::patch('/super-admin/companies/{company}/toggle-status', [\App\Http\Controllers\SuperAdminReportController::class, 'toggleStatus'])
            ->name('admin.super.companies.toggle');

        // User Management Routes
        Route::get('/super-admin/users', [\App\Http\Controllers\UserManagementController::class, 'index'])
            ->name('admin.super.users.index');
        Route::post('/super-admin/users', [\App\Http\Controllers\UserManagementController::class, 'store'])
            ->name('admin.super.users.store');
        Route::put('/super-admin/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'update'])
            ->name('admin.super.users.update');
        Route::delete('/super-admin/users/{user}', [\App\Http\Controllers\UserManagementController::class, 'destroy'])
            ->name('admin.super.users.destroy');
    });

    Route::middleware([\App\Http\Middleware\AdminUmkmMiddleware::class])->group(function () {
        Route::get('/admin', [\App\Http\Controllers\CompanyReportController::class, 'index'])
            ->name('admin.company.index');
    });
});