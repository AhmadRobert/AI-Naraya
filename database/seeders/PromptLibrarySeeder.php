<?php

namespace Database\Seeders;

use App\Models\Prompt;
use Illuminate\Database\Seeder;

class PromptLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $prompts = [

            // ---------------------------------------------------------
            // PRODUK
            // ---------------------------------------------------------
            [
                'category' => 'Produk',
                'title' => 'Perkenalan Produk Baru',
                'template' =>
"KONTEKS:
Anda adalah seorang social media copywriter untuk brand {nama_produk}, menulis untuk audiens {target_audiens} di Instagram.

TUGAS:
Buat caption untuk memperkenalkan produk baru dengan keunggulan utama: {keunggulan}.

FORMAT OUTPUT:
- Hook (1 kalimat pembuka yang menarik perhatian)
- Body (2-3 kalimat menjelaskan keunggulan produk)
- CTA (1 kalimat ajakan bertindak)
- 5 hashtag relevan di baris terakhir

BATASAN:
- Jangan gunakan klaim berlebihan (contoh: 'terbaik di dunia', 'nomor 1')
- Jangan gunakan lebih dari 2 emoji
- Hindari kalimat lebih dari 20 kata per kalimat",
            ],
            [
                'category' => 'Produk',
                'title' => 'Detail & Spesifikasi Produk',
                'template' =>
"KONTEKS:
Anda adalah copywriter e-commerce yang menulis deskripsi produk {nama_produk} untuk katalog online, ditujukan kepada {target_audiens}.

TUGAS:
Tulis deskripsi produk berdasarkan spesifikasi/bahan: {spesifikasi} dan harga: {harga}. Fokus pada manfaat, bukan sekadar fitur.

FORMAT OUTPUT:
- Judul singkat produk (maks 8 kata)
- Paragraf deskripsi (3-4 kalimat)
- Poin-poin manfaat (bullet, maksimal 4 poin)
- Informasi harga di baris terakhir

BATASAN:
- Jangan mengarang spesifikasi yang tidak disebutkan di data
- Hindari istilah teknis yang tidak dijelaskan
- Jangan menyalin gaya deskripsi brand kompetitor",
            ],
            [
                'category' => 'Produk',
                'title' => 'Perbandingan dengan Kompetitor',
                'template' =>
"KONTEKS:
Anda adalah brand strategist yang menulis konten perbandingan untuk {nama_produk}, ditujukan kepada {target_audiens} yang sedang mempertimbangkan pilihan produk.

TUGAS:
Buat konten yang menonjolkan keunggulan pembeda produk kami: {keunggulan}, dibandingkan alternatif di pasar secara elegan.

FORMAT OUTPUT:
- Pembuka yang menyebutkan masalah umum yang dihadapi audiens
- 2-3 poin keunggulan produk kami (bullet)
- Penutup dengan ajakan mencoba

BATASAN:
- Jangan menyebut nama brand/kompetitor tertentu secara langsung
- Jangan menjatuhkan atau merendahkan kompetitor
- Hindari nada yang terkesan menyerang atau defensif",
            ],

            // ---------------------------------------------------------
            // PROMO
            // ---------------------------------------------------------
            [
                'category' => 'Promo',
                'title' => 'Flash Sale / Diskon Terbatas',
                'template' =>
"KONTEKS:
Anda adalah digital marketing specialist yang menulis caption promo untuk {nama_produk}, ditujukan kepada {target_audiens} di media sosial.

TUGAS:
Buat caption promo flash sale dengan diskon {diskon}, berlaku pada periode {periode}, yang menciptakan urgensi agar audiens segera bertindak.

FORMAT OUTPUT:
- Hook FOMO di kalimat pertama (contoh nuansa: waktu terbatas, kuota terbatas)
- Detail promo (produk, diskon, periode)
- CTA jelas dan spesifik (contoh: 'Klik link di bio sebelum promo berakhir')

BATASAN:
- Jangan mencantumkan tanggal/waktu yang tidak ada di data
- Hindari kesan memaksa atau clickbait berlebihan
- Jangan gunakan huruf kapital semua (ALL CAPS) di lebih dari satu kata",
            ],
            [
                'category' => 'Promo',
                'title' => 'Bundling / Paket Hemat',
                'template' =>
"KONTEKS:
Anda adalah copywriter promosi untuk {nama_produk}, menulis untuk {target_audiens} yang sensitif terhadap harga.

TUGAS:
Buat caption promosi paket bundling berisi {isi_paket}, dengan menekankan penghematan dari perbandingan harga: {perbandingan_harga}.

FORMAT OUTPUT:
- Kalimat pembuka yang menyebutkan isi paket
- Penjelasan singkat penghematan yang didapat (angka/persentase jika tersedia)
- CTA ajakan membeli paket

BATASAN:
- Jangan mengarang angka penghematan yang tidak ada di data
- Hindari kesan bahwa produk single lebih rendah kualitasnya
- Maksimal 60 kata total",
            ],
            [
                'category' => 'Promo',
                'title' => 'Promo Hari Spesial/Momen',
                'template' =>
"KONTEKS:
Anda adalah social media specialist untuk {nama_produk}, menulis untuk {target_audiens} dalam rangka momen: {momen}.

TUGAS:
Buat caption promo yang mengaitkan produk dengan momen tersebut, menyertakan penawaran khusus: {penawaran}.

FORMAT OUTPUT:
- Kalimat pembuka yang menyebut momen/hari spesial secara relevan
- Kaitan momen dengan produk (1-2 kalimat)
- Detail penawaran khusus dan CTA

BATASAN:
- Jangan gunakan referensi agama/budaya secara sensitif tanpa konteks yang jelas
- Hindari nada yang terkesan memanfaatkan momen secara berlebihan (over-promotional)
- Jangan ubah nama momen yang diberikan",
            ],

            // ---------------------------------------------------------
            // EDUKASI
            // ---------------------------------------------------------
            [
                'category' => 'Edukasi',
                'title' => 'Tips Seputar Produk/Layanan',
                'template' =>
"KONTEKS:
Anda adalah content specialist yang membuat konten edukasi ringan untuk {target_audiens}, terkait topik: {topik} dan produk {nama_produk}.

TUGAS:
Buat konten tips yang informatif dan bermanfaat, ditutup dengan ajakan mencoba produk secara halus (soft-selling).

FORMAT OUTPUT:
- Judul singkat (maks 8 kata)
- 3-5 poin tips (bullet, tiap poin 1 kalimat)
- Penutup soft-selling (1 kalimat)

BATASAN:
- Jangan berikan klaim medis/kesehatan yang tidak dapat diverifikasi
- Hindari nada menggurui
- Jangan jadikan penutup terlalu hard-selling (fokus tetap edukasi)",
            ],
            [
                'category' => 'Edukasi',
                'title' => 'Mitos vs Fakta',
                'template' =>
"KONTEKS:
Anda adalah content specialist yang membuat konten edukasi untuk {target_audiens}, seputar topik {topik} yang relevan dengan industri {industri}.

TUGAS:
Buat konten 'Mitos vs Fakta' berisi 3 pasang mitos-fakta, ditutup dengan posisi produk {nama_produk} sebagai solusi.

FORMAT OUTPUT:
- 3 pasang format: 'Mitos: ...' lalu 'Fakta: ...'
- Alasan singkat/logis untuk tiap fakta (1 kalimat)
- Penutup yang mengaitkan dengan {nama_produk}

BATASAN:
- Jangan mencantumkan sumber/data statistik yang tidak diverifikasi
- Hindari nada yang terkesan menakut-nakuti audiens
- Jangan buat klaim yang tidak berhubungan langsung dengan topik",
            ],
            [
                'category' => 'Edukasi',
                'title' => 'Cara Pakai / How-To',
                'template' =>
"KONTEKS:
Anda adalah customer education specialist yang menulis panduan untuk {target_audiens} pengguna {nama_produk}.

TUGAS:
Buat panduan langkah demi langkah cara menggunakan {nama_produk} agar hasil maksimal.

FORMAT OUTPUT:
- Langkah bernomor (minimal 3, maksimal 6 langkah)
- Bahasa instruksional yang jelas dan singkat per langkah
- 1 tips tambahan di bagian akhir

BATASAN:
- Jangan mengarang langkah yang tidak sesuai fungsi produk
- Hindari istilah teknis tanpa penjelasan
- Jangan sertakan peringatan keamanan yang tidak relevan dengan produk",
            ],

            // ---------------------------------------------------------
            // TESTIMONI
            // ---------------------------------------------------------
            [
                'category' => 'Testimoni',
                'title' => 'Testimoni Pelanggan (Cerita)',
                'template' =>
"KONTEKS:
Anda adalah copywriter yang menyusun ulang cerita pelanggan {nama_pelanggan} menjadi caption untuk {target_audiens}.

TUGAS:
Susun ulang inti testimoni berikut menjadi caption yang menarik dan autentik: {testimoni_asli}. Tutup dengan ajakan bagi audiens untuk mencoba produk {nama_produk} juga.

FORMAT OUTPUT:
- Kalimat pembuka bernada cerita (bukan iklan)
- Isi testimoni yang disusun ulang (2-3 kalimat)
- CTA ajakan mencoba di akhir

BATASAN:
- Jangan menambahkan detail yang tidak ada di testimoni asli
- Hindari nada yang terkesan dibuat-buat atau berlebihan
- Jangan ubah makna inti dari testimoni asli",
            ],
            [
                'category' => 'Testimoni',
                'title' => 'Before & After',
                'template' =>
"KONTEKS:
Anda adalah copywriter yang membuat konten before-after untuk {nama_produk}, ditujukan kepada {target_audiens}.

TUGAS:
Buat caption yang membandingkan kondisi sebelum: {kondisi_sebelum} dan sesudah: {kondisi_sesudah}, dengan durasi pemakaian: {durasi}.

FORMAT OUTPUT:
- Kalimat pembuka yang menyebut durasi pemakaian
- Perbandingan kondisi sebelum vs sesudah (2 kalimat)
- Penutup yang jujur, tanpa janji berlebihan

BATASAN:
- Jangan membuat klaim hasil yang tidak realistis atau bersifat mutlak (contoh: 'pasti berhasil 100%')
- Hindari kata-kata superlatif berlebihan
- Jangan gunakan data before-after yang tidak ada di input",
            ],
            [
                'category' => 'Testimoni',
                'title' => 'Social Proof / Angka Pencapaian',
                'template' =>
"KONTEKS:
Anda adalah brand copywriter untuk {nama_produk}, menulis untuk {target_audiens} yang butuh validasi sebelum membeli.

TUGAS:
Buat caption yang menonjolkan social proof dalam bentuk pencapaian: {pencapaian}.

FORMAT OUTPUT:
- Kalimat pembuka yang langsung menampilkan angka pencapaian
- 1-2 kalimat konteks yang memperkuat kepercayaan
- CTA singkat di akhir

BATASAN:
- Jangan mengubah atau membulatkan angka pencapaian secara signifikan
- Hindari nada membanggakan diri secara berlebihan
- Jangan bandingkan langsung dengan kompetitor",
            ],

            // ---------------------------------------------------------
            // STORYBOARD VIDEO
            // ---------------------------------------------------------
            [
                'category' => 'Storyboard Video',
                'title' => 'Storyboard Iklan Produk (Umum)',
                'template' =>
"KONTEKS:
Anda adalah video content creator yang membuat storyboard iklan untuk {nama_produk}, ditujukan kepada {target_audiens}.

TUGAS:
Buat storyboard video berdasarkan ide/konsep: {ide}, dengan durasi target {durasi} detik.

FORMAT OUTPUT:
- Minimal 8 scene bernomor
- Tiap scene berisi: Visual (deskripsi adegan), Camera (jenis shot), Mood (suasana)
- Scene terakhir wajib berisi CTA visual/teks

BATASAN:
- Jangan buat scene yang membutuhkan properti/lokasi yang tidak realistis untuk produksi kecil
- Hindari durasi per scene lebih dari 5 detik
- Jangan keluar dari konsep/ide yang sudah ditentukan",
            ],
            [
                'category' => 'Storyboard Video',
                'title' => 'Storyboard Testimoni/UGC Style',
                'template' =>
"KONTEKS:
Anda adalah video content creator yang membuat storyboard bergaya UGC (User Generated Content) untuk {nama_produk}, ditujukan kepada {target_audiens}.

TUGAS:
Buat storyboard video testimoni berdasarkan poin testimoni: {poin_testimoni}.

FORMAT OUTPUT:
- Minimal 6 scene bernomor
- Tiap scene berisi: Visual, Camera (gaya casual/handheld), Mood
- Dialog/voice over singkat per scene jika relevan

BATASAN:
- Jangan buat gaya visual yang terkesan terlalu 'produksi studio' (harus tetap terasa autentik/UGC)
- Hindari klaim testimoni yang tidak ada di poin_testimoni
- Jangan tambahkan endorsement yang tidak diminta",
            ],
            [
                'category' => 'Storyboard Video',
                'title' => 'Storyboard Promo/Flash Sale',
                'template' =>
"KONTEKS:
Anda adalah video content creator yang membuat storyboard untuk Reels/TikTok promosi {nama_produk}, ditujukan kepada {target_audiens}.

TUGAS:
Buat storyboard video pendek dengan penawaran: {penawaran}, berirama cepat dan penuh urgensi.

FORMAT OUTPUT:
- Minimal 6 scene bernomor
- Tiap scene berisi: Visual, Camera, Mood
- Teks CTA yang muncul di layar pada scene terakhir

BATASAN:
- Jangan buat durasi total lebih dari 30 detik
- Hindari scene yang tidak relevan dengan penawaran
- Jangan gunakan musik/lagu berlisensi tertentu dalam deskripsi",
            ],

            // ---------------------------------------------------------
            // GABUNGKAN FOTO 
            // ---------------------------------------------------------
            [
                'category' => 'Gabungkan Foto',
                'title' => 'Gabungkan 2 Karakter dalam 1 Frame',
                'template' =>
"KONTEKS:
Kamu menerima 2 gambar referensi. Gambar pertama berisi karakter/model 1, gambar kedua berisi karakter/model 2. Kedua gambar HARUS dipakai sebagai referensi wajah dan tubuh yang PERSIS SAMA dengan aslinya.

TUGAS:
Gabungkan KEDUA karakter tersebut ke dalam SATU frame/foto yang sama, posisi: {posisi} (contoh: 'karakter 1 di sisi kiri, karakter 2 di sisi kanan'). Latar belakang diganti menjadi: {latar_belakang}.

HASIL YANG DIHARAPKAN:
- KEDUA karakter harus tetap terlihat utuh dan jelas dalam 1 frame yang sama
- Wajah, bentuk tubuh, warna kulit, dan ciri fisik masing-masing karakter TIDAK berubah dari gambar referensi aslinya
- Pencahayaan dan bayangan menyatu secara natural antara kedua karakter dan latar belakang baru
- Proporsi ukuran antara kedua karakter tetap realistis

BATASAN (WAJIB DIIKUTI):
- JANGAN menghilangkan salah satu karakter
- JANGAN menggabungkan wajah/tubuh kedua karakter menjadi satu orang tunggal
- JANGAN mengubah wajah, warna kulit, atau bentuk tubuh asli dari kedua karakter
- JANGAN mengubah pakaian karakter kecuali diminta secara eksplisit",
            ],
            [
                'category' => 'Gabungkan Foto',
                'title' => 'Gabungkan Karakter dengan Produk',
                'template' =>
"KONTEKS:
Gambar pertama adalah karakter/model, gambar kedua adalah objek/produk yang harus muncul di foto yang sama.

TUGAS:
Gabungkan karakter dari gambar pertama dengan objek dari gambar kedua ke dalam satu frame. Karakter {aksi_karakter} (contoh: 'memegang produk dengan kedua tangan', 'berdiri di samping produk'). Latar belakang: {latar_belakang}.

HASIL YANG DIHARAPKAN:
- Karakter dan objek/produk sama-sama terlihat jelas dan lengkap
- Interaksi antara karakter dan objek terlihat natural sesuai instruksi aksi
- Detail bentuk, warna, dan tulisan/label pada produk TIDAK berubah dari gambar referensi asli
- Wajah dan tubuh karakter TIDAK berubah dari gambar referensi asli

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah bentuk, warna, atau tulisan pada produk
- JANGAN menghilangkan produk dari frame akhir
- JANGAN mengubah wajah atau ciri fisik karakter
- JANGAN membuat ukuran objek tidak proporsional (terlalu besar/kecil dari ukuran realistisnya)",
            ],
            [
                'category' => 'Gabungkan Foto',
                'title' => 'Gabungkan 3 Karakter dalam 1 Grup',
                'template' =>
"KONTEKS:
Kamu menerima 3 gambar referensi, masing-masing berisi 1 karakter berbeda (karakter 1, karakter 2, karakter 3). Ketiganya harus muncul bersamaan dalam hasil akhir.

TUGAS:
Gabungkan KETIGA karakter menjadi satu foto grup dalam satu frame yang sama, formasi: {formasi} (contoh: 'berdiri berjajar', 'duduk bersebelahan'). Latar belakang: {latar_belakang}. Suasana/mood foto: {mood}.

HASIL YANG DIHARAPKAN:
- KETIGA karakter harus tetap terlihat utuh, jelas, dan proporsional dalam 1 frame yang sama
- Wajah dan ciri fisik masing-masing karakter TIDAK berubah dari gambar referensi aslinya
- Pencahayaan konsisten dan menyatu secara natural di antara ketiga karakter dan latar belakang baru

BATASAN (WAJIB DIIKUTI):
- JANGAN menghilangkan satupun dari ketiga karakter
- JANGAN menggabungkan wajah/tubuh antar karakter menjadi satu orang
- JANGAN mengubah wajah, warna kulit, atau bentuk tubuh dari ketiga karakter aslinya
- JANGAN mengubah pakaian karakter kecuali diminta secara eksplisit",
            ],
            [
                'category' => 'Gabungkan Foto',
                'title' => 'Karakter Melakukan Aktivitas Bersama',
                'template' =>
"KONTEKS:
Kamu menerima 2 gambar referensi karakter berbeda yang wajah dan tubuhnya harus dipertahankan persis seperti aslinya.

TUGAS:
Gabungkan kedua karakter ke dalam satu frame yang sama, sedang melakukan aktivitas: {aktivitas} (contoh: 'bersalaman', 'berjalan bersama', 'duduk mengobrol'). Latar belakang: {latar_belakang}.

HASIL YANG DIHARAPKAN:
- Kedua karakter terlihat utuh dan berinteraksi secara natural sesuai aktivitas yang diminta
- Wajah dan ciri fisik masing-masing karakter TIDAK berubah dari gambar referensi aslinya
- Pose dan interaksi terlihat realistis, bukan kaku/dipaksakan

BATASAN (WAJIB DIIKUTI):
- JANGAN menghilangkan salah satu karakter
- JANGAN mengubah wajah atau ciri fisik dari kedua karakter
- JANGAN membuat interaksi yang tidak sesuai instruksi aktivitas",
            ],
            [
                'category' => 'Gabungkan Foto',
                'title' => 'Gabungkan Foto dengan Logo/Watermark Brand',
                'template' =>
"KONTEKS:
Gambar pertama adalah foto utama (karakter/produk), gambar kedua adalah logo brand yang harus ditempatkan di foto.

TUGAS:
Tempatkan logo dari gambar kedua ke posisi {posisi_logo} (contoh: 'pojok kanan bawah') pada foto pertama, dengan ukuran {ukuran_logo} (contoh: 'kecil dan tidak mengganggu subjek utama').

HASIL YANG DIHARAPKAN:
- Foto utama (subjek, latar belakang, komposisi) tetap 100% sama seperti aslinya
- Logo terlihat jelas namun proporsional, tidak menutupi elemen penting foto
- Logo menyatu secara natural (opacity dan pencahayaan disesuaikan bila perlu)

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah elemen apapun di foto utama selain menambahkan logo
- JANGAN mengubah bentuk atau warna asli logo
- JANGAN menempatkan logo menutupi wajah/produk utama",
            ],

            // ---------------------------------------------------------
            // EDIT FOTO 
            // ---------------------------------------------------------
            [
                'category' => 'Edit Foto',
                'title' => 'Ganti Latar Belakang',
                'template' =>
"KONTEKS:
Kamu menerima 1 gambar referensi berisi subjek utama (orang/produk) yang harus dipertahankan persis seperti aslinya.

TUGAS:
Ganti HANYA latar belakang foto menjadi: {latar_belakang_baru}. Subjek utama TIDAK boleh diubah sama sekali.

HASIL YANG DIHARAPKAN:
- Subjek utama (wajah, tubuh, pose, pakaian, warna) 100% identik dengan gambar asli
- Latar belakang baru menyatu secara natural dengan pencahayaan pada subjek
- Bayangan/refleksi pada subjek disesuaikan agar realistis dengan latar belakang baru

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah wajah, pose, atau pakaian subjek utama
- JANGAN memotong/crop bagian tubuh subjek yang sebelumnya terlihat penuh
- JANGAN menambahkan objek lain selain latar belakang yang diminta",
            ],
            [
                'category' => 'Edit Foto',
                'title' => 'Ubah Ekspresi/Pose Tanpa Ubah Wajah',
                'template' =>
"KONTEKS:
Kamu menerima 1 gambar referensi wajah/karakter yang harus dijadikan acuan identitas (wajah asli WAJIB dipertahankan).

TUGAS:
Ubah {jenis_perubahan} (contoh: 'ekspresi menjadi tersenyum', 'pose menjadi menghadap samping') pada karakter, TANPA mengubah identitas wajah aslinya.

HASIL YANG DIHARAPKAN:
- Wajah tetap bisa dikenali sebagai orang yang sama persis dengan gambar referensi
- Perubahan ekspresi/pose terlihat natural, bukan dipaksakan
- Pencahayaan dan detail kulit tetap konsisten dengan foto asli

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah bentuk wajah, mata, hidung, atau fitur wajah lainnya
- JANGAN mengubah warna kulit atau rambut
- JANGAN mengubah latar belakang kecuali diminta secara eksplisit",
            ],
            [
                'category' => 'Edit Foto',
                'title' => 'Hapus/Tambah Objek di Foto',
                'template' =>
"KONTEKS:
Kamu menerima 1 gambar referensi yang harus dipertahankan komposisi utamanya, kecuali objek yang diminta untuk diubah.

TUGAS:
{aksi} objek berikut dari/ke dalam foto: {nama_objek}. Posisi: {posisi_objek}.

HASIL YANG DIHARAPKAN:
- Area di sekitar objek yang diubah menyatu secara natural (tidak ada bekas edit yang terlihat kasar)
- Subjek utama & elemen lain di foto yang TIDAK disebutkan tetap sama persis seperti aslinya
- Pencahayaan dan bayangan pada objek baru (jika menambahkan) konsisten dengan foto asli

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah elemen lain di luar objek yang diminta
- JANGAN mengubah subjek utama (wajah/tubuh/pose) dalam foto
- JANGAN mengubah rasio maupun crop foto asli",
            ],
            [
                'category' => 'Edit Foto',
                'title' => 'Ubah Warna/Jenis Pakaian',
                'template' =>
"KONTEKS:
Kamu menerima 1 gambar referensi karakter yang wajah, pose, dan latar belakangnya harus dipertahankan persis seperti aslinya.

TUGAS:
Ubah {bagian_pakaian} (contoh: 'warna baju', 'jenis atasan') menjadi: {perubahan_diminta}.

HASIL YANG DIHARAPKAN:
- Wajah, pose, dan latar belakang 100% identik dengan gambar asli
- Perubahan pakaian terlihat natural mengikuti bentuk tubuh dan pose asli
- Tekstur dan pencahayaan pada pakaian baru konsisten dengan foto asli

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah wajah, pose, atau latar belakang
- JANGAN mengubah bagian pakaian yang tidak disebutkan
- JANGAN mengubah proporsi tubuh karakter",
            ],
            [
                'category' => 'Edit Foto',
                'title' => 'Ubah Gaya Visual/Filter Foto',
                'template' =>
"KONTEKS:
Kamu menerima 1 gambar referensi yang komposisi dan subjeknya harus tetap dipertahankan, hanya gaya visual keseluruhan yang diubah.

TUGAS:
Ubah gaya visual foto menjadi: {gaya_visual} (contoh: 'cinematic warm tone', 'vintage film look', 'hitam putih dramatis').

HASIL YANG DIHARAPKAN:
- Subjek, pose, dan komposisi foto tetap sama persis dengan aslinya
- Perubahan hanya pada warna, kontras, dan mood visual sesuai gaya yang diminta
- Hasil akhir terlihat profesional dan konsisten di seluruh bagian foto

BATASAN (WAJIB DIIKUTI):
- JANGAN mengubah wajah, pose, atau elemen komposisi
- JANGAN mengubah latar belakang atau menambah/menghapus objek
- JANGAN membuat perubahan warna yang membuat wajah terlihat tidak natural",
            ],
        ];

        foreach ($prompts as $prompt) {

            Prompt::updateOrCreate(
                [
                    'category' => $prompt['category'],
                    'title' => $prompt['title'],
                ],
                [
                    'template' => $prompt['template'],
                    'variables' => $this->extractVariables($prompt['template']),
                ]
            );
        }
    }

    protected function extractVariables(string $template): array
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $template, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }
}