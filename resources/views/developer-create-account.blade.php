<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Developer Create Account | AI-Naraya</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        primary: '#006874',
                        'primary-soft': '#20C4D8',
                        'primary-glow': '#98F0FF',
                        surface: '#F7F9FB',
                        outline: '#BBC9CC',
                        'outline-strong': '#6C797C',
                        'on-surface': '#191C1E',
                        'on-muted': '#3C494B',
                        danger: '#BA1A1A',
                        success: '#0B7A46',
                    },
                    boxShadow: {
                        premium: '0 24px 80px rgba(0, 104, 116, 0.16)',
                        glow: '0 20px 60px rgba(32, 196, 216, 0.28)',
                    }
                }
            }
        };
    </script>

    <style>
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

        .glass-panel {
            background: rgba(255, 255, 255, 0.82);
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
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-10">
        <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-primary-soft/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-32 bottom-10 h-96 w-96 rounded-full bg-primary/10 blur-3xl"></div>

        <section class="relative z-10 w-full max-w-[560px]">
            <div class="mb-8 flex items-center justify-center gap-3">
                <div class="premium-gradient flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-glow">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                </div>
                <div>
                    <p class="text-xl font-extrabold tracking-tight text-primary">AI-Naraya</p>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-on-muted/70">Developer Access</p>
                </div>
            </div>

            <div class="glass-panel rounded-[2rem] p-6 shadow-premium sm:p-8">
                <div class="mb-8 text-center">
                    <span class="inline-flex items-center justify-center gap-2 rounded-full bg-primary-soft/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-primary">
                        <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                        Developer Panel
                    </span>

                    <h1 class="mt-5 text-3xl font-extrabold tracking-[-0.03em] text-on-surface sm:text-4xl">
                        Buat Akun Login
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-on-muted sm:text-base">
                        Halaman ini khusus developer untuk membuat akun yang bisa login ke dashboard AI-Naraya.
                    </p>
                </div>

                @if (session('success'))
                    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-4 text-sm font-semibold text-success">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined mt-0.5 text-[20px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                            <div>
                                <p>{{ session('success') }}</p>

                                @if (session('created_email'))
                                    <div class="mt-3 rounded-xl bg-white/80 px-3 py-3 text-on-muted">
                                        <p><strong>Nama:</strong> {{ session('created_name') }}</p>
                                        <p><strong>Email:</strong> {{ session('created_email') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('developer.account.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="developer_key" class="mb-2 block text-sm font-bold text-on-muted">Kode Developer</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline-strong">key</span>
                            <input
                                id="developer_key"
                                name="developer_key"
                                type="password"
                                value="{{ old('developer_key') }}"
                                required
                                placeholder="Masukkan kode developer"
                                class="input-focus w-full rounded-2xl border border-outline bg-white/80 py-4 pl-12 pr-4 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-muted/45 focus:border-primary-soft"
                            >
                        </div>
                        @error('developer_key')
                            <p class="mt-2 text-sm font-semibold text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="mb-2 block text-sm font-bold text-on-muted">Nama User</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline-strong">person</span>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                placeholder="Contoh: Ahmad Robertha"
                                class="input-focus w-full rounded-2xl border border-outline bg-white/80 py-4 pl-12 pr-4 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-muted/45 focus:border-primary-soft"
                            >
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold text-on-muted">Email Login</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline-strong">mail</span>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
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
                                required
                                minlength="6"
                                placeholder="Minimal 6 karakter"
                                class="input-focus w-full rounded-2xl border border-outline bg-white/80 py-4 pl-12 pr-12 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-muted/45 focus:border-primary-soft"
                            >
                            <button
                                id="togglePasswordBtn"
                                type="button"
                                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xl p-1 text-outline-strong transition hover:bg-surface hover:text-primary"
                                aria-label="Tampilkan password"
                            >
                                <span id="passwordIcon" class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm font-semibold text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-bold text-on-muted">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline-strong">verified_user</span>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                minlength="6"
                                placeholder="Ulangi password"
                                class="input-focus w-full rounded-2xl border border-outline bg-white/80 py-4 pl-12 pr-4 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-muted/45 focus:border-primary-soft"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="premium-gradient flex w-full items-center justify-center gap-3 rounded-2xl px-5 py-4 text-base font-extrabold text-white shadow-glow transition-all hover:-translate-y-0.5 hover:brightness-105 active:translate-y-0 active:scale-[0.99]"
                    >
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person_add</span>
                        Buat Akun Login
                    </button>
                </form>

                <div class="mt-7 flex flex-col gap-3 text-center sm:flex-row sm:items-center sm:justify-center">
                    <a href="{{ route('login') }}" class="font-extrabold text-primary hover:text-primary-soft hover:underline">
                        Kembali ke Login
                    </a>
                    <span class="hidden h-1 w-1 rounded-full bg-outline sm:block"></span>
                    <a href="{{ route('beli') }}" class="font-bold text-on-muted hover:text-primary hover:underline">
                        Lihat Halaman Beli
                    </a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs font-semibold text-on-muted/65">
                © {{ date('Y') }} AI-Naraya. Developer account creator.
            </p>
        </section>
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