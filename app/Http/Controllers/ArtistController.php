<?php

namespace App\Http\Controllers;

/**
 * FIX MAPPING PENTING: sebelumnya controller ini punya generate() dan
 * checkStatus() versi ComfyUI (async, submit ke /api/prompt lalu polling),
 * dan route POST /artist/generate di web.php sempat mengarah ke sini.
 *
 * Itu SALAH ARAH - Produk Artist ada di daftar fitur yang harus pakai
 * Grok (bukan Comfy), dan implementasi Grok yang benar SUDAH ADA di
 * ImageGenerationController::generateArtist() (sinkron, hasil langsung
 * dalam satu response, cocok dengan artist.blade.php versi Oltha yang
 * tidak melakukan polling).
 *
 * Method generate()/checkStatus()/uploadToComfyCloud()/getDimensionsFromRatio()
 * versi ComfyUI DIHAPUS dari sini. Controller ini sekarang cuma bertugas
 * menampilkan halaman - proses generate ditangani lewat route terpisah
 * yang mengarah ke ImageGenerationController::generateArtist() (lihat
 * routes/web.php).
 *
 * FIX TAMBAHAN: view() diubah ke huruf kecil 'artist' (sebelumnya 'Artist'
 * huruf besar). Nama file blade yang dipakai adalah artist.blade.php
 * (huruf kecil), dan di server Linux resolusi nama view itu case-sensitive
 * — kalau dibiarkan 'Artist' bisa menyebabkan error "View not found".
 * Ini juga menyamakan konvensi dengan view gabung/edit/carousel yang
 * semuanya huruf kecil.
 */
class ArtistController extends Controller
{
    public function index()
    {
        return view('artist');
    }
}