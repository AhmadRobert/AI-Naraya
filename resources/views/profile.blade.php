@extends('layouts.app')

@section('title', 'Pengaturan Akun & Profil | AI-Naraya')

@section('content')
<div class="mx-auto max-w-5xl space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-primary-container/30 bg-primary-container/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-primary">
                <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                Pengaturan Akun
            </div>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-on-surface sm:text-4xl">
                Profil Saya
            </h1>
            <p class="mt-1.5 text-sm leading-relaxed text-on-surface-variant">
                Kelola informasi identitas, alamat email, dan keamanan kata sandi akun AI-Naraya kamu.
            </p>
        </div>
    </div>

    <!-- Account Summary Card -->
    <div class="glass-panel overflow-hidden rounded-3xl border border-outline-variant/60 bg-surface-container-lowest p-6 shadow-soft sm:p-8">
        <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
            @php
                $nameParts = preg_split('/\s+/', trim($user->name ?? 'User')) ?: [];
                $initials = collect($nameParts)
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                    ->implode('');
                $initials = $initials ?: 'AI';
            @endphp
            <div class="relative flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-gradient-to-br from-primary via-primary-container to-primary-fixed text-2xl font-black text-white shadow-premium">
                {{ $initials }}
                <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white ring-4 ring-white" title="Akun Aktif">
                    <span class="material-symbols-outlined text-[14px]">check</span>
                </span>
            </div>

            <div class="min-w-0 flex-1 space-y-2">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="truncate text-2xl font-extrabold text-on-surface">{{ $user->name }}</h2>
                    <span class="inline-flex items-center gap-1 rounded-full border border-primary/20 bg-primary/10 px-3 py-0.5 text-xs font-bold text-primary">
                        <span class="material-symbols-outlined text-[14px]">verified</span>
                        Pro Creator
                    </span>
                </div>
                <p class="truncate text-sm font-semibold text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-outline">mail</span>
                    {{ $user->email }}
                </p>
                <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-on-surface-variant/80 pt-1">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-outline">calendar_today</span>
                        Terdaftar {{ $user->created_at ? $user->created_at->format('d M Y') : 'Baru saja' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-outline">security</span>
                        Autentikasi Terproteksi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Forms Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Edit Profile Info -->
        <div class="glass-panel flex flex-col justify-between rounded-3xl border border-outline-variant/60 bg-surface-container-lowest p-6 shadow-soft sm:p-8">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="border-b border-outline-variant/50 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined">person</span>
                        </span>
                        <div>
                            <h3 class="text-lg font-extrabold text-on-surface">Informasi Pribadi</h3>
                            <p class="text-xs font-semibold text-on-surface-variant">Perbarui nama dan alamat email profil kamu.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="name" class="mb-2 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nama Lengkap</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline">badge</span>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full rounded-2xl border border-outline-variant/80 bg-surface-container-lowest py-3.5 pl-12 pr-4 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                placeholder="Nama lengkap kamu"
                            >
                        </div>
                        @error('name')
                            <p class="mt-2 text-xs font-bold text-error flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Alamat Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline">alternate_email</span>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full rounded-2xl border border-outline-variant/80 bg-surface-container-lowest py-3.5 pl-12 pr-4 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                placeholder="nama@email.com"
                            >
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs font-bold text-error flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="focus-ring inline-flex items-center justify-center gap-2 rounded-2xl bg-primary px-6 py-3.5 text-sm font-extrabold text-white shadow-soft transition hover:-translate-y-0.5 hover:brightness-105 active:translate-y-0 active:scale-[0.98]"
                    >
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="glass-panel flex flex-col justify-between rounded-3xl border border-outline-variant/60 bg-surface-container-lowest p-6 shadow-soft sm:p-8">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="border-b border-outline-variant/50 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined">lock_reset</span>
                        </span>
                        <div>
                            <h3 class="text-lg font-extrabold text-on-surface">Ubah Password</h3>
                            <p class="text-xs font-semibold text-on-surface-variant">Pastikan password kamu kuat dan tidak mudah ditebak.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="current_password" class="mb-2 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Password Saat Ini</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                required
                                class="w-full rounded-2xl border border-outline-variant/80 bg-surface-container-lowest py-3.5 pl-12 pr-12 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                placeholder="Masukkan password saat ini"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('current_password', 'currentPasswordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xl p-1 text-outline transition hover:bg-surface-container-high hover:text-on-surface"
                                aria-label="Toggle password visibility"
                            >
                                <span id="currentPasswordIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-xs font-bold text-error flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Password Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline">key</span>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full rounded-2xl border border-outline-variant/80 bg-surface-container-lowest py-3.5 pl-12 pr-12 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                placeholder="Minimal 6 karakter"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('password', 'passwordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xl p-1 text-outline transition hover:bg-surface-container-high hover:text-on-surface"
                                aria-label="Toggle password visibility"
                            >
                                <span id="passwordIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs font-bold text-error flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">error</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-xs font-bold uppercase tracking-wider text-on-surface-variant">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-outline">published_with_changes</span>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="w-full rounded-2xl border border-outline-variant/80 bg-surface-container-lowest py-3.5 pl-12 pr-12 text-sm font-semibold text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/10"
                                placeholder="Ulangi password baru"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('password_confirmation', 'confirmPasswordIcon')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xl p-1 text-outline transition hover:bg-surface-container-high hover:text-on-surface"
                                aria-label="Toggle password visibility"
                            >
                                <span id="confirmPasswordIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="focus-ring inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant bg-surface-container-high px-6 py-3.5 text-sm font-extrabold text-on-surface shadow-soft transition hover:-translate-y-0.5 hover:bg-surface-container-highest active:translate-y-0 active:scale-[0.98]"
                    >
                        <span class="material-symbols-outlined text-[20px]">key_vertical</span>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.textContent = isHidden ? 'visibility_off' : 'visibility';
    }
</script>
@endsection
