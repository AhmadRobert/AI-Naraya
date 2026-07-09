@php
    $price = '1.000.000';
    $adminWhatsApp = '6281249874100'; // Ganti dengan nomor WhatsApp admin kamu, format 62 bukan 0.
    $waText = rawurlencode('Halo admin AI-Naraya, saya mau beli paket AI-Naraya Pro Rp ' . $price . '.');
    $waLink = 'https://wa.me/' . $adminWhatsApp . '?text=' . $waText;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli AI-Naraya | Smart Visual Studio</title>
    <meta name="description" content="AI-Naraya Pro membantu membuat visual AI yang rapi, cepat, dan terlihat profesional untuk kreator, UMKM, dan admin sosial media.">

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#061014',
                        navy: '#08131C',
                        panel: '#101B26',
                        panel2: '#162432',
                        primary: '#006874',
                        cyan: '#20C4D8',
                        glow: '#98F0FF',
                        orange: '#FF6A1A',
                        pink: '#F21E6B',
                        mint: '#26E5A8',
                    },
                    boxShadow: {
                        premium: '0 34px 110px rgba(0,0,0,.42)',
                        cyan: '0 22px 75px rgba(32,196,216,.28)',
                        fire: '0 22px 75px rgba(255,106,26,.35)',
                    },
                }
            }
        }
    </script>

    <style>
        * { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: #061014;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 520, 'GRAD' 0, 'opsz' 24;
        }

        .hero-bg {
            background:
                radial-gradient(circle at 18% 18%, rgba(32,196,216,.26), transparent 34rem),
                radial-gradient(circle at 78% 28%, rgba(242,30,107,.18), transparent 35rem),
                radial-gradient(circle at 50% 100%, rgba(0,104,116,.28), transparent 36rem),
                linear-gradient(135deg, #061014 0%, #08131C 54%, #06191E 100%);
        }

        .grid-bg {
            background-image:
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
            background-size: 42px 42px;
        }

        .glass {
            background: rgba(255,255,255,.075);
            border: 1px solid rgba(255,255,255,.13);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .teal-gradient {
            background: linear-gradient(135deg, #20C4D8 0%, #008FA0 45%, #006874 100%);
        }

        .cta-gradient {
            background: linear-gradient(135deg, #FF7A18 0%, #F04438 48%, #F21E6B 100%);
        }

        .shine::after {
            content: '';
            position: absolute;
            top: -140%;
            left: -60%;
            width: 42%;
            height: 380%;
            transform: rotate(24deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.62), transparent);
            animation: shineMove 4.2s ease-in-out infinite;
        }

        @keyframes shineMove {
            0%, 45% { left: -60%; }
            100% { left: 145%; }
        }

        .float-card {
            animation: floatCard 6s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
    </style>
</head>
<body class="min-h-screen bg-ink text-white antialiased">
    <!-- Promo strip tanpa tombol beli di topbar -->
    <div class="fixed left-0 top-0 z-50 w-full border-b border-white/10 bg-cyan/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-center gap-4 px-4 py-3 text-center text-xs font-black uppercase tracking-[0.16em] text-white sm:text-sm">
            <span>🔥 Sekali bayar</span>
            <span class="hidden sm:inline">•</span>
            <span>4 tools AI</span>
            <span class="hidden sm:inline">•</span>
            <span>Dashboard premium</span>
        </div>
    </div>

    <!-- Header: tombol beli di topbar sudah dihapus -->
    <header class="fixed left-0 top-[42px] z-40 w-full border-b border-white/10 bg-[#08131C]/86 backdrop-blur-2xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
            <a href="{{ route('login') }}" class="group flex items-center gap-3">
                <div class="teal-gradient flex h-12 w-12 items-center justify-center rounded-2xl shadow-cyan transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                </div>
                <div>
                    <p class="text-xl font-black tracking-tight">AI-Naraya</p>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-cyan-100/65">Smart Visual Studio</p>
                </div>
            </a>

            <a href="{{ route('login') }}" class="rounded-2xl border border-white/14 bg-white/8 px-5 py-3 text-sm font-black text-white/88 transition hover:-translate-y-0.5 hover:bg-white/14 active:translate-y-0">
                Sudah punya akun
            </a>
        </div>
    </header>

    <!-- Hero -->
    <main class="hero-bg grid-bg overflow-hidden pt-[118px]">
        <section class="relative px-5 py-20 lg:py-28">
            <div class="pointer-events-none absolute -left-32 top-28 h-96 w-96 rounded-full bg-cyan/18 blur-3xl"></div>
            <div class="pointer-events-none absolute right-0 top-52 h-[28rem] w-[28rem] rounded-full bg-pink/14 blur-3xl"></div>

            <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-[1.03fr_.97fr]">
                <div>
                    <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-cyan/35 bg-cyan/10 px-5 py-2 text-xs font-black uppercase tracking-[0.24em] text-glow">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">bolt</span>
                        AI Creative Workspace
                    </div>

                    <h1 class="max-w-3xl text-5xl font-black leading-[1.04] tracking-[-0.06em] text-white sm:text-6xl xl:text-7xl">
                        Buat visual AI yang rapi, cepat, dan terlihat profesional.
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg font-semibold leading-8 text-cyan-50/76">
                        AI-Naraya membantu kamu membuat konten visual untuk kebutuhan sosial media, produk, campaign, dan carousel dalam satu dashboard yang clean dan premium.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3 text-sm font-black text-cyan-50/82">
                        <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">Gabungkan Foto</span>
                        <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">Edit Foto</span>
                        <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">Produk Artist</span>
                        <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">Carousel</span>
                    </div>

                    <div class="mt-9 flex flex-col gap-4 sm:flex-row">
                        <a href="#harga" class="cta-gradient shine relative inline-flex items-center justify-center overflow-hidden rounded-2xl px-8 py-4 text-base font-black text-white shadow-fire transition hover:-translate-y-1 hover:brightness-110 active:translate-y-0">
                            <span class="relative z-10">🚀 Lihat Paket Pro</span>
                        </a>
                        <a href="#fitur" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/9 px-8 py-4 text-base font-black text-white transition hover:-translate-y-1 hover:bg-white/14 active:translate-y-0">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">play_arrow</span>
                            Lihat Fitur
                        </a>
                    </div>

                    <div class="mt-8 grid max-w-xl grid-cols-3 gap-3">
                        <div class="rounded-3xl border border-white/10 bg-white/8 p-4">
                            <p class="text-3xl font-black">4+</p>
                            <p class="mt-1 text-xs font-bold text-white/55">AI Tools</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/8 p-4">
                            <p class="text-3xl font-black">Fast</p>
                            <p class="mt-1 text-xs font-bold text-white/55">Workflow</p>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/8 p-4">
                            <p class="text-3xl font-black">Pro</p>
                            <p class="mt-1 text-xs font-bold text-white/55">Quality</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="float-card glass relative mx-auto max-w-xl rounded-[2.25rem] p-5 shadow-premium">
                        <div class="rounded-[1.75rem] border border-white/10 bg-[#0B1420] p-5">
                            <div class="mb-5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-red-400"></span>
                                    <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
                                    <span class="h-3 w-3 rounded-full bg-green-400"></span>
                                </div>
                                <span class="rounded-full bg-cyan/15 px-3 py-1 text-xs font-black text-glow">AI-Naraya Pro</span>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/8">
                                    <div class="mb-9 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan/16 text-glow">
                                        <span class="material-symbols-outlined text-3xl">filter_none</span>
                                    </div>
                                    <p class="font-black">Gabungkan Foto</p>
                                    <p class="mt-1 text-xs font-semibold text-white/50">Blend multi image</p>
                                </div>
                                <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/8">
                                    <div class="mb-9 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan/16 text-glow">
                                        <span class="material-symbols-outlined text-3xl">image</span>
                                    </div>
                                    <p class="font-black">Edit Foto</p>
                                    <p class="mt-1 text-xs font-semibold text-white/50">Prompt retouch</p>
                                </div>
                                <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/8">
                                    <div class="mb-9 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan/16 text-glow">
                                        <span class="material-symbols-outlined text-3xl">photo_camera</span>
                                    </div>
                                    <p class="font-black">Produk Artist</p>
                                    <p class="mt-1 text-xs font-semibold text-white/50">Campaign product AI</p>
                                </div>
                                <div class="rounded-3xl bg-white/8 p-4 ring-1 ring-white/8">
                                    <div class="mb-9 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan/16 text-glow">
                                        <span class="material-symbols-outlined text-3xl">view_carousel</span>
                                    </div>
                                    <p class="font-black">Carousel</p>
                                    <p class="mt-1 text-xs font-semibold text-white/50">Storyboard render</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -bottom-7 left-4 right-4 mx-auto max-w-sm rounded-3xl border border-orange/25 bg-gradient-to-br from-orange/95 to-pink/95 px-6 py-5 text-center shadow-fire">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-white/80">Paket Pro</p>
                        <p class="mt-1 text-3xl font-black">Rp {{ $price }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="fitur" class="bg-white px-5 py-24 text-slate-950">
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto mb-14 max-w-3xl text-center">
                    <span class="inline-flex rounded-full border border-cyan/30 bg-cyan/10 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-primary">Fitur Utama</span>
                    <h2 class="mt-5 text-4xl font-black tracking-[-0.04em] sm:text-5xl">Semua tools visual dalam satu dashboard.</h2>
                    <p class="mt-4 text-lg font-medium leading-8 text-slate-600">Cocok untuk kreator, UMKM, admin sosial media, mahasiswa, dan tim kecil yang butuh hasil visual cepat.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan/15 text-primary"><span class="material-symbols-outlined text-3xl">filter_none</span></div>
                        <h3 class="text-xl font-black">Gabungkan Foto</h3>
                        <p class="mt-3 leading-7 text-slate-600">Upload beberapa gambar dan buat komposisi baru yang lebih rapi.</p>
                    </div>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan/15 text-primary"><span class="material-symbols-outlined text-3xl">image</span></div>
                        <h3 class="text-xl font-black">Edit Foto</h3>
                        <p class="mt-3 leading-7 text-slate-600">Retouch, ubah background, tingkatkan kualitas, dan edit dengan prompt.</p>
                    </div>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan/15 text-primary"><span class="material-symbols-outlined text-3xl">photo_camera</span></div>
                        <h3 class="text-xl font-black">Produk Artist</h3>
                        <p class="mt-3 leading-7 text-slate-600">Buat visual campaign produk dengan model/artis terlihat premium.</p>
                    </div>
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan/15 text-primary"><span class="material-symbols-outlined text-3xl">view_carousel</span></div>
                        <h3 class="text-xl font-black">Carousel</h3>
                        <p class="mt-3 leading-7 text-slate-600">Upload storyboard PDF dan bantu render konsep visual carousel.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="harga" class="hero-bg grid-bg px-5 py-24">
            <div class="mx-auto max-w-5xl">
                <div class="mb-10 text-center">
                    <span class="inline-flex rounded-full border border-orange/40 bg-orange/10 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-orange">Paket Terbaik</span>
                    <h2 class="mt-5 text-4xl font-black tracking-[-0.04em] text-white sm:text-5xl">Sekali bayar, langsung akses.</h2>
                    <p class="mt-4 text-lg font-semibold text-cyan-50/70">Tanpa ribet. Akun dibuat oleh admin setelah pembayaran dikonfirmasi.</p>
                </div>

                <div class="glass rounded-[2.25rem] p-6 shadow-premium sm:p-9">
                    <div class="grid gap-8 lg:grid-cols-[.95fr_1.05fr] lg:items-center">
                        <div class="rounded-[2rem] border border-white/12 bg-white/8 p-7">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-300">AI-Naraya Pro</p>
                            <div class="mt-5 flex items-end gap-2">
                                <span class="text-6xl font-black tracking-[-0.06em] text-white">Rp {{ $price }}</span>
                            </div>
                            <p class="mt-3 text-sm font-bold text-cyan-50/58">Akses dashboard premium AI-Naraya.</p>

                            <a href="{{ $waLink }}" target="_blank" rel="noopener" class="cta-gradient shine relative mt-8 flex w-full items-center justify-center overflow-hidden rounded-2xl px-7 py-5 text-lg font-black text-white shadow-fire transition hover:-translate-y-1 hover:brightness-110 active:translate-y-0">
                                <span class="relative z-10">🚀 Chat Admin via WhatsApp</span>
                            </a>
                        </div>

                        <div class="space-y-4">
                            @foreach ([
                                '4 fitur AI utama langsung aktif',
                                'Dashboard bersih, premium, dan mudah dipakai',
                                'Cocok untuk konten sosial media, produk, dan carousel',
                                'Akun login dibuat setelah pembayaran dikonfirmasi',
                                'Bantuan setup awal sampai bisa login'
                            ] as $item)
                                <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/8 p-4">
                                    <span class="material-symbols-outlined mt-0.5 text-mint" style="font-variation-settings:'FILL' 1;">check_circle</span>
                                    <p class="font-black text-white">{{ $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="bg-white px-5 py-24 text-slate-950">
            <div class="mx-auto max-w-4xl">
                <div class="mb-10 text-center">
                    <span class="inline-flex rounded-full border border-cyan/30 bg-cyan/10 px-4 py-2 text-xs font-black uppercase tracking-[0.2em] text-primary">FAQ</span>
                    <h2 class="mt-5 text-4xl font-black tracking-[-0.04em]">Pertanyaan umum.</h2>
                </div>

                <div class="space-y-4">
                    <details class="group rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <summary class="cursor-pointer list-none text-lg font-black">Apakah langsung bisa login?</summary>
                        <p class="mt-3 leading-7 text-slate-600">Akun akan dibuat oleh admin setelah pembayaran dikonfirmasi.</p>
                    </details>
                    <details class="group rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <summary class="cursor-pointer list-none text-lg font-black">Apakah ini langganan bulanan?</summary>
                        <p class="mt-3 leading-7 text-slate-600">Halaman ini dibuat untuk paket sekali bayar. Biaya API AI tetap bisa berbeda jika aplikasinya memakai layanan API berbayar.</p>
                    </details>
                    <details class="group rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <summary class="cursor-pointer list-none text-lg font-black">Bisa dibantu kalau bingung?</summary>
                        <p class="mt-3 leading-7 text-slate-600">Bisa. Klik tombol WhatsApp, nanti admin bantu proses pembelian dan aktivasi akun.</p>
                    </details>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-[#08131C] px-5 py-8 text-center text-sm font-semibold text-white/50">
        © {{ date('Y') }} AI-Naraya. Smart Visual Studio.
    </footer>
</body>
</html>
