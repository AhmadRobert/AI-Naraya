<!DOCTYPE html>
<html class="light scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'id') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AI-Naraya | Professional Image Studio')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#f7f9fb',
                        surface: '#f7f9fb',
                        'surface-bright': '#f7f9fb',
                        'surface-dim': '#d8dadc',
                        'surface-container-lowest': '#ffffff',
                        'surface-container-low': '#f2f4f6',
                        'surface-container': '#eceef0',
                        'surface-container-high': '#e6e8ea',
                        'surface-container-highest': '#e0e3e5',
                        'surface-variant': '#e0e3e5',

                        primary: '#006874',
                        'primary-container': '#20c4d8',
                        'primary-fixed': '#98f0ff',
                        'primary-fixed-dim': '#45d8ed',
                        'inverse-primary': '#45d8ed',

                        secondary: '#575e70',
                        'secondary-container': '#d9dff5',
                        'secondary-fixed': '#dce2f7',
                        'secondary-fixed-dim': '#c0c6db',

                        tertiary: '#505f76',
                        'tertiary-container': '#a4b4cd',
                        'tertiary-fixed': '#d3e4fe',
                        'tertiary-fixed-dim': '#b7c8e1',

                        outline: '#6c797c',
                        'outline-variant': '#bbc9cc',

                        error: '#ba1a1a',
                        'error-container': '#ffdad6',

                        'on-background': '#191c1e',
                        'on-surface': '#191c1e',
                        'on-surface-variant': '#3c494b',
                        'on-primary': '#ffffff',
                        'on-primary-container': '#004c55',
                        'on-primary-fixed': '#001f24',
                        'on-primary-fixed-variant': '#004f58',
                        'on-secondary': '#ffffff',
                        'on-secondary-container': '#5c6274',
                        'on-secondary-fixed': '#141b2b',
                        'on-secondary-fixed-variant': '#404758',
                        'on-tertiary': '#ffffff',
                        'on-tertiary-container': '#36465b',
                        'on-tertiary-fixed': '#0b1c30',
                        'on-tertiary-fixed-variant': '#38485d',
                        'on-error': '#ffffff',
                        'on-error-container': '#93000a',
                        'inverse-surface': '#2d3133',
                        'inverse-on-surface': '#eff1f3',
                        'surface-tint': '#006874'
                    },
                    boxShadow: {
                        soft: '0 4px 24px rgba(15, 23, 42, 0.06)',
                        premium: '0 20px 60px rgba(15, 23, 42, 0.10)'
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem'
                    },
                    spacing: {
                        base: '8px',
                        gutter: '16px',
                        'container-padding': '24px',
                        'section-gap': '48px'
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'display-lg': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'headline-lg': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'headline-md': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'body-lg': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'body-md': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'label-md': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        'label-sm': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    fontSize: {
                        'display-lg': ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '800' }],
                        'headline-lg': ['32px', { lineHeight: '40px', letterSpacing: '-0.01em', fontWeight: '700' }],
                        'headline-md': ['24px', { lineHeight: '32px', fontWeight: '700' }],
                        'body-lg': ['18px', { lineHeight: '28px', fontWeight: '400' }],
                        'body-md': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                        'label-md': ['14px', { lineHeight: '20px', letterSpacing: '0.01em', fontWeight: '600' }],
                        'label-sm': ['12px', { lineHeight: '16px', fontWeight: '700' }]
                    }
                }
            }
        };
    </script>

    <style>
        :root {
            color-scheme: light;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #bbc9cc transparent;
        }

        *::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        *::-webkit-scrollbar-thumb {
            background: #bbc9cc;
            border: 3px solid transparent;
            border-radius: 999px;
            background-clip: padding-box;
        }

        *::-webkit-scrollbar-track {
            background: transparent;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(32, 196, 216, 0.13), transparent 34rem),
                radial-gradient(circle at bottom right, rgba(152, 240, 255, 0.20), transparent 34rem),
                #f7f9fb;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 450, 'GRAD' 0, 'opsz' 24;
            line-height: 1;
        }

        .canvas-bg {
            background-image: radial-gradient(circle, rgba(108, 121, 124, 0.38) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .active-pill {
            box-shadow: 0 10px 26px rgba(32, 196, 216, 0.22);
        }

        .nav-link-active {
            background: linear-gradient(135deg, rgba(32, 196, 216, 0.20), rgba(217, 223, 245, 0.80));
            color: #004c55;
            box-shadow: inset 0 0 0 1px rgba(0, 104, 116, 0.18), 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .nav-link-active .nav-icon {
            background: #006874;
            color: #ffffff;
        }

        .nav-link-inactive:hover .nav-icon {
            background: rgba(32, 196, 216, 0.16);
            color: #006874;
        }

        .focus-ring {
            outline: none;
        }

        .focus-ring:focus-visible {
            box-shadow: 0 0 0 4px rgba(32, 196, 216, 0.22);
        }

        @media (max-width: 767px) {
            .sidebar-open {
                overflow: hidden;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="text-on-surface antialiased">
    @php
        $navigationItems = [
            [
                'label' => 'Gabungkan Foto',
                'href' => url('/gabung'),
                'pattern' => 'gabung*',
                'icon' => 'auto_awesome_motion',
                'description' => 'Blend multi image',
            ],
            [
                'label' => 'Edit Foto',
                'href' => url('/edit'),
                'pattern' => 'edit*',
                'icon' => 'add_photo_alternate',
                'description' => 'Retouch dan prompt edit',
            ],
            [
                'label' => 'Produk Artist',
                'href' => url('/artist'),
                'pattern' => 'artist*',
                'icon' => 'photo_camera',
                'description' => 'Campaign product AI',
            ],
            [
    'label' => 'Carousel',
    'href' => url('/carousel'),
    'pattern' => 'carousel*',
    'icon' => 'view_carousel',
    'description' => 'Storyboard to render',
],
        ];

        $authUser = auth()->user();
        $displayName = $authUser?->name ?? 'AI-Naraya User';
        $displayEmail = $authUser?->email ?? 'Belum login';
        $nameParts = preg_split('/\s+/', trim($displayName)) ?: [];
        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
        $initials = $initials ?: 'AI';
    @endphp

    <div id="mobileSidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/40 backdrop-blur-sm md:hidden"></div>

    <!-- Top Navigation -->
    <header class="fixed inset-x-0 top-0 z-50 h-16 border-b border-outline-variant/50 glass-panel">
        <div class="flex h-full items-center justify-between px-4 md:px-container-padding">
            <div class="flex items-center gap-3">
                <button
                    id="openSidebarButton"
                    type="button"
                    class="focus-ring inline-flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant bg-surface-container-lowest text-on-surface-variant shadow-soft transition hover:bg-surface-container-high md:hidden"
                    aria-label="Buka menu"
                    aria-expanded="false"
                >
                    <span class="material-symbols-outlined">menu</span>
                </button>

                <a href="{{ url('/gabung') }}" class="group flex items-center gap-3 rounded-2xl pr-3 transition" aria-label="AI-Naraya Home">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary text-on-primary shadow-soft transition-transform group-hover:scale-105">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1, 'wght' 600;">auto_awesome</span>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-xl font-extrabold tracking-tight text-primary">AI Naraya</span>
                        <span class="hidden text-[11px] font-bold uppercase tracking-[0.18em] text-on-surface-variant sm:block">SMART VISUAL STUDIO</span>
                    </span>
                </a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <div class="flex items-center gap-2 rounded-full border border-outline-variant/70 bg-surface-container-lowest px-3 py-2 shadow-soft">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary-container opacity-60"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-primary"></span>
                    </span>
                    <span class="text-label-sm text-on-surface-variant">Studio Ready</span>
                </div>

                <div class="h-8 w-px bg-outline-variant"></div>

                <div class="flex items-center gap-3 rounded-full border border-outline-variant/70 bg-surface-container-lowest py-1.5 pl-2 pr-3 shadow-soft">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-container text-xs font-extrabold text-white shadow-soft">
                        {{ $initials }}
                    </div>
                    <div class="hidden leading-tight lg:block">
                        <p class="max-w-[140px] truncate text-sm font-extrabold text-on-surface">{{ $displayName }}</p>
                        <p class="max-w-[140px] truncate text-[11px] font-semibold text-on-surface-variant">{{ $displayEmail }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside
        id="sidebar"
        class="fixed left-0 top-16 z-50 h-[calc(100vh-4rem)] w-72 -translate-x-full border-r border-outline-variant/60 bg-surface-container-low/95 p-4 shadow-premium backdrop-blur-xl transition-transform duration-300 md:z-40 md:translate-x-0 md:shadow-none"
        aria-label="Workspace navigation"
    >
        <div class="flex h-full flex-col">
            <div class="mb-5 rounded-3xl border border-outline-variant/60 bg-surface-container-lowest p-4 shadow-soft">
                <p class="text-label-sm uppercase tracking-[0.18em] text-on-surface-variant">Workspace</p>
                <h2 class="mt-1 text-lg font-extrabold text-on-surface">Dashboard Editor</h2>
                <p class="mt-1 text-sm leading-5 text-on-surface-variant">Pilih tools sesuai kebutuhan generate gambar kamu.</p>
            </div>

            <nav class="flex flex-col gap-2">
                @foreach ($navigationItems as $item)
                    @php
                        $isActive = request()->is($item['pattern']);
                    @endphp

                    <a
                        href="{{ $item['href'] }}"
                        class="focus-ring group flex items-center gap-3 rounded-2xl px-3 py-3 transition-all duration-200 {{ $isActive ? 'nav-link-active font-bold' : 'nav-link-inactive text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}"
                        aria-current="{{ $isActive ? 'page' : 'false' }}"
                    >
                        <span class="nav-icon flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-surface-container-lowest text-on-surface-variant shadow-soft transition-all">
                            <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                        </span>

                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold">{{ $item['label'] }}</span>
                            <span class="block truncate text-xs font-medium opacity-70">{{ $item['description'] }}</span>
                        </span>

                        @if ($isActive)
                            <span class="ml-auto h-2 w-2 rounded-full bg-primary"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto pt-5">
                <div class="rounded-3xl border border-outline-variant/70 bg-surface-container-lowest p-4 shadow-soft">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary-container text-sm font-extrabold text-white shadow-soft">
                            {{ $initials }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-extrabold text-on-surface">{{ $displayName }}</p>
                            <p class="truncate text-xs font-semibold text-on-surface-variant">{{ $displayEmail }}</p>
                        </div>
                    </div>

                    <div class="mt-4 h-px bg-outline-variant/70"></div>

                    @if (Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf

                            <button
                                type="submit"
                                class="focus-ring group flex w-full items-center justify-center gap-2 rounded-2xl border border-error/25 bg-error-container/60 px-4 py-3 text-sm font-extrabold text-on-error-container transition-all hover:-translate-y-0.5 hover:bg-error-container hover:shadow-soft active:translate-y-0 active:scale-[0.98]"
                            >
                                <span class="material-symbols-outlined text-[20px] transition-transform group-hover:-translate-x-0.5">logout</span>
                                Logout
                            </button>
                        </form>
                    @else
                        <div class="mt-4 rounded-2xl border border-outline-variant/70 bg-surface-container-low px-4 py-3 text-center text-xs font-bold text-on-surface-variant">
                            Route logout belum tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="canvas-bg min-h-screen pt-16 md:pl-72">
        <div class="min-h-[calc(100vh-4rem)] px-4 py-6 sm:px-6 lg:px-container-padding">
            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-primary/25 bg-primary-fixed/35 px-4 py-3 text-sm font-bold text-on-primary-fixed shadow-soft">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-2xl border border-error/25 bg-error-container/70 px-4 py-3 text-sm font-bold text-on-error-container shadow-soft">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');
            const openButton = document.getElementById('openSidebarButton');

            if (!sidebar || !overlay || !openButton) return;

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                body.classList.add('sidebar-open');
                openButton.setAttribute('aria-expanded', 'true');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                body.classList.remove('sidebar-open');
                openButton.setAttribute('aria-expanded', 'false');
            };

            openButton.addEventListener('click', () => {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            overlay.addEventListener('click', closeSidebar);

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    overlay.classList.add('hidden');
                    body.classList.remove('sidebar-open');
                    openButton.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
