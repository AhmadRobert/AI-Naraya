<?php

namespace App\Http\Controllers;

/**
 * FIX: view() dikembalikan ke 'Artist' (huruf besar A). Perbaikan
 * sebelumnya mengubah ini jadi 'artist' (huruf kecil) dengan asumsi nama
 * file blade-nya huruf kecil, tapi berdasarkan struktur project yang
 * sebenarnya, file blade-nya adalah Artist.blade.php (huruf besar). Di
 * server Linux (case-sensitive), view('artist') akan gagal - "View not
 * found" - walau tidak masalah di Windows/Mac saat development lokal.
 *
 * Proses generate() ditangani lewat route terpisah yang mengarah ke
 * ImageGenerationController::generateArtist() -> ImageService::generateWithGrok()
 * (lihat routes/web.php), controller ini cuma bertugas menampilkan halaman.
 */
class ArtistController extends Controller
{
    public function index()
    {
        return view('Artist');
    }
}