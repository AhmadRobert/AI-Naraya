    <!DOCTYPE html>
    <html lang="id" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | AI-Naraya</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: '#006874',
                            'primary-soft': '#20C4D8',
                            'primary-glow': '#98F0FF',
                            surface: '#F7F9FB',
                            'surface-card': '#FFFFFF',
                            'surface-soft': '#F2F4F6',
                            'surface-high': '#E6E8EA',
                            outline: '#BBC9CC',
                            'outline-strong': '#6C797C',
                            'on-surface': '#191C1E',
                            'on-muted': '#3C494B',
                            danger: '#BA1A1A',
                            'danger-soft': '#FFDAD6'
                        },
                        boxShadow: {
                            premium: '0 24px 80px rgba(0, 104, 116, 0.16)',
                            soft: '0 12px 40px rgba(25, 28, 30, 0.08)',
                            glow: '0 20px 60px rgba(32, 196, 216, 0.28)'
                        },
                        borderRadius: {
                            '3xl': '1.5rem',
                            '4xl': '2rem'
                        }
                    }
                }
            };
        </script>

        <style>
            * {
                scroll-behavior: smooth;
            }

            body {
                font-family: 'Inter', sans-serif;
            }

            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 430, 'GRAD' 0, 'opsz' 24;
            }

            .canvas-bg {
                background-color: #F7F9FB;
                background-image:
                    radial-gradient(circle at 20% 20%, rgba(32, 196, 216, 0.16), transparent 28rem),
                    radial-gradient(circle at 80% 10%, rgba(0, 104, 116, 0.12), transparent 24rem),
                    radial-gradient(circle at 50% 100%, rgba(152, 240, 255, 0.26), transparent 28rem),
                    radial-gradient(circle, rgba(187, 201, 204, 0.75) 1px, transparent 1px);
                background-size: auto, auto, auto, 24px 24px;
            }

            .hero-orb {
                position: absolute;
                border-radius: 9999px;
                filter: blur(1px);
                opacity: 0.9;
                animation: floatOrb 7s ease-in-out infinite;
            }

            .hero-orb:nth-child(2) {
                animation-delay: -2.4s;
            }

            .hero-orb:nth-child(3) {
                animation-delay: -4.1s;
            }

            @keyframes floatOrb {
                0%, 100% {
                    transform: translate3d(0, 0, 0) scale(1);
                }
                50% {
                    transform: translate3d(14px, -18px, 0) scale(1.04);
                }
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(187, 201, 204, 0.68);
                backdrop-filter: blur(18px);
                -webkit-backdrop-filter: blur(18px);
            }

            .premium-gradient {
                background: linear-gradient(135deg, #20C4D8 0%, #008FA0 42%, #006874 100%);
            }

            .input-focus:focus {
                box-shadow: 0 0 0 4px rgba(32, 196, 216, 0.16);
            }
        </style>
    </head>

    <body class="min-h-screen canvas-bg text-on-surface antialiased">
        <main class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-primary-soft/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-32 bottom-10 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>

            <div class="relative z-10 mx-auto grid min-h-screen w-full max-w-[1440px] grid-cols-1 lg:grid-cols-2">
                <!-- Hero Section -->
                <section class="relative hidden min-h-screen flex-col justify-between overflow-hidden px-10 py-10 lg:flex xl:px-16">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl premium-gradient text-white shadow-glow">
                            <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                        </div>
                        <div>
                            <p class="text-xl font-extrabold tracking-tight text-primary">AI-Naraya</p>
                            <p class="text-xs font-semibold uppercase tracking-[0.26em] text-on-muted/70">Creative Workspace</p>
                        </div>
                    </div>

                    <div class="relative my-10 flex flex-1 items-center">
                        <div class="absolute left-10 top-16 h-28 w-28 hero-orb bg-primary-soft/25"></div>
                        <div class="absolute right-14 top-24 h-20 w-20 hero-orb bg-primary/18"></div>
                        <div class="absolute bottom-28 left-40 h-16 w-16 hero-orb bg-primary-glow/60"></div>

                        <div class="glass-panel relative w-full overflow-hidden rounded-[2rem] p-9 shadow-premium">
                            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary-soft/20 blur-2xl"></div>
                            <div class="absolute -bottom-24 left-12 h-60 w-60 rounded-full bg-primary/10 blur-2xl"></div>

                            <div class="relative">
                                <span class="inline-flex items-center gap-2 rounded-full border border-primary-soft/30 bg-white/70 px-4 py-2 text-xs font-bold uppercase tracking-[0.22em] text-primary">
                                    <span class="h-2 w-2 rounded-full bg-primary-soft"></span>
                                    AI Creative Workspace
                                </span>

                                <h1 class="mt-7 max-w-xl text-5xl font-extrabold leading-[1.06] tracking-[-0.04em] text-on-surface">
                                    Buat visual AI yang rapi, cepat, dan terlihat profesional.
                                </h1>

                                <p class="mt-5 max-w-lg text-base leading-7 text-on-muted">
                                    Masuk untuk melanjutkan proses gabungkan foto, edit foto, product artist, dan carousel dalam satu dashboard yang clean dan premium.
                                </p>

                                <div class="mt-7 flex flex-wrap gap-3">
                                    <span class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-primary shadow-sm">Gabungkan Foto</span>
                                    <span class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-primary shadow-sm">Edit Foto</span>
                                    <span class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-primary shadow-sm">Product Artist</span>
                                    <span class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-primary shadow-sm">Carousel</span>
                                </div>
                            </div>

                            <div class="relative mt-10 grid grid-cols-3 gap-4">
                                <div class="rounded-3xl border border-white/70 bg-white/70 p-4 shadow-soft">
                                    <div class="mb-8 h-10 w-10 rounded-2xl bg-primary-soft/15 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined">image</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-on-surface">4+</p>
                                    <p class="mt-1 text-xs font-semibold text-on-muted">AI Tools</p>
                                </div>

                                <div class="rounded-3xl border border-white/70 bg-white/70 p-4 shadow-soft">
                                    <div class="mb-8 h-10 w-10 rounded-2xl bg-primary-soft/15 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined">bolt</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-on-surface">Fast</p>
                                    <p class="mt-1 text-xs font-semibold text-on-muted">Workflow</p>
                                </div>

                                <div class="rounded-3xl border border-white/70 bg-white/70 p-4 shadow-soft">
                                    <div class="mb-8 h-10 w-10 rounded-2xl bg-primary-soft/15 text-primary flex items-center justify-center">
                                        <span class="material-symbols-outlined">verified</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-on-surface">Pro</p>
                                    <p class="mt-1 text-xs font-semibold text-on-muted">Quality</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm font-medium text-on-muted/70">© {{ date('Y') }} AI-Naraya. Intelligence evolved.</p>
                </section>

                <!-- Login Section -->
                <section class="flex min-h-screen items-center justify-center px-5 py-8 sm:px-8 lg:px-10">
                    <div class="w-full max-w-[500px]">
                        <div class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl premium-gradient text-white shadow-glow">
                                <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                            </div>
                            <div>
                                <p class="text-xl font-extrabold tracking-tight text-primary">AI-Naraya</p>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-on-muted/70">Creative Workspace</p>
                            </div>
                        </div>

                        <div class="glass-panel rounded-[2rem] p-6 shadow-premium sm:p-8">
                            <div class="mb-8 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-primary-soft/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-primary">
                                    Login
                                </span>
                                <h2 class="mt-5 text-3xl font-extrabold tracking-[-0.03em] text-on-surface sm:text-4xl">
                                    Selamat Datang
                                </h2>
                                <p class="mt-3 text-sm leading-6 text-on-muted sm:text-base">
                                    Masuk untuk melanjutkan membuat konten visual AI.
                                </p>
                            </div>

                            @if (session('status'))
                                <div class="mb-5 rounded-2xl border border-primary-soft/30 bg-primary-soft/10 px-4 py-3 text-sm font-medium text-primary">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="email" class="mb-2 block text-sm font-bold text-on-muted">Email</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline-strong">mail</span>
                                        <input
                                            id="email"
                                            name="email"
                                            type="email"
                                            value="{{ old('email') }}"
                                            autocomplete="email"
                                            required
                                            autofocus
                                            placeholder="nama@email.com"
                                            class="input-focus w-full rounded-2xl border border-outline bg-white/80 py-4 pl-12 pr-4 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-muted/45 focus:border-primary-soft"
                                        >
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm font-semibold text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="mb-2 block text-sm font-bold text-on-muted">Password</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline-strong">lock</span>
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            autocomplete="current-password"
                                            required
                                            placeholder="Masukkan password"
                                            class="input-focus w-full rounded-2xl border border-outline bg-white/80 py-4 pl-12 pr-12 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-muted/45 focus:border-primary-soft"
                                        >
                                        <button
                                            id="togglePasswordBtn"
                                            type="button"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xl p-1 text-outline-strong transition hover:bg-surface-soft hover:text-primary"
                                            aria-label="Tampilkan password"
                                        >
                                            <span id="passwordIcon" class="material-symbols-outlined">visibility</span>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm font-semibold text-danger">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="flex items-center justify-between gap-4">   
                                </div>

                                <button
                                    type="submit"
                                    class="premium-gradient flex w-full items-center justify-center gap-3 rounded-2xl px-5 py-4 text-base font-extrabold text-white shadow-glow transition-all hover:-translate-y-0.5 hover:brightness-105 active:translate-y-0 active:scale-[0.99]"
                                >
                                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">login</span>
                                    Masuk
                                </button>
                            </form>

                            <p class="mt-7 text-center text-sm font-medium text-on-muted">
                                Belum punya akun?
                                <a href="{{ route('beli') }}" class="font-extrabold text-primary hover:text-primary-soft hover:underline">
                                    Beli Sekarang
                                </a>
                            </p>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-semibold text-on-muted/65">
                            <a href="#" class="transition hover:text-primary">Privacy Policy</a>
                            <span class="h-1 w-1 rounded-full bg-outline"></span>
                            <a href="#" class="transition hover:text-primary">Terms of Service</a>
                            <span class="h-1 w-1 rounded-full bg-outline"></span>
                            <span>© {{ date('Y') }} AI-Naraya</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const passwordInput = document.getElementById('password');
                const togglePasswordBtn = document.getElementById('togglePasswordBtn');
                const passwordIcon = document.getElementById('passwordIcon');

                togglePasswordBtn?.addEventListener('click', () => {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    passwordIcon.textContent = isHidden ? 'visibility_off' : 'visibility';
                    togglePasswordBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                });
            });
        </script>
    </body>
    </html>
