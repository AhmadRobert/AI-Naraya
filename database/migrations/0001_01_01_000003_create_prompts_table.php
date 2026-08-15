<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();

            // Kategori sesuai checklist Modul Minggu 2/4:
            // Produk, Promo, Edukasi, Testimoni, Storyboard Video
            $table->string('category');

            $table->string('title');

            // Isi template prompt, boleh mengandung placeholder
            // seperti {nama_produk}, {target_audiens}, dst.
            $table->text('template');

            // Daftar nama variabel yang dipakai di template ini,
            // contoh: ["nama_produk", "target_audiens", "tujuan_iklan"]
            $table->json('variables')->nullable();

            // Rating efektivitas (opsional, diisi manual/nanti otomatis)
            $table->decimal('rating', 3, 2)->nullable();

            $table->unsignedInteger('usage_count')->default(0);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};