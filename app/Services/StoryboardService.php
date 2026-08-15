<?php

namespace App\Services;

class StoryboardService
{
    protected AIManager $ai;

    public function __construct(AIManager $ai)
    {
        $this->ai = $ai;
    }

    public function generate(string $idea): string
    {
        // FIX: sebelumnya prompt memaksa "Minimal 8 Scene" untuk semua
        // permintaan. Sekarang jumlah scene diserahkan ke pertimbangan AI
        // berdasarkan konteks/durasi yang disebutkan pengguna sendiri di
        // dalam idenya (mis. "iklan 15 detik", "iklan 30 detik", dll).
        // Kalau user tidak menyebutkan durasi/jumlah scene sama sekali,
        // AI diminta memakai jumlah yang wajar untuk iklan pendek.
        $prompt = "

Buat storyboard video iklan berdasarkan permintaan pengguna berikut.

Permintaan pengguna:
{$idea}

Ketentuan jumlah scene:
- JANGAN memaksakan jumlah scene tertentu (tidak harus 8, boleh kurang atau lebih).
- Kalau permintaan pengguna menyebutkan durasi (misalnya 10 detik, 15 detik, 30 detik, 1 menit, dst), tentukan jumlah scene yang proporsional dan realistis untuk durasi tersebut (perkirakan sendiri berapa detik wajar per scene untuk jenis iklan ini).
- Kalau pengguna tidak menyebutkan durasi/jumlah scene sama sekali, gunakan jumlah scene yang paling sesuai dengan kompleksitas ide tersebut, sewajarnya untuk sebuah iklan pendek.
- Gunakan pertimbangan terbaikmu sebagai storyboard writer profesional untuk memutuskan jumlah scene akhir.

Setiap scene harus memiliki:

Scene
Visual
Camera
Mood

";

        return $this->ai->generateStoryboard($prompt);
    }

    /**
     * Passthrough ke AIManager - melaporkan provider mana yang benar-benar
     * memproses request generate() TERAKHIR (Grok atau Agnes AI fallback).
     */
    public function lastProvider(): string
    {
        return $this->ai->lastProvider();
    }
}