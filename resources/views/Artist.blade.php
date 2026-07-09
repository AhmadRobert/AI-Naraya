@extends('layouts.app')

@section('content')
<div class="max-w-[1440px] mx-auto grid grid-cols-1 xl:grid-cols-12 gap-8 pb-12">

    <!-- LEFT PANEL -->
    <aside class="xl:col-span-5 2xl:col-span-4">
        <div class="xl:sticky xl:top-24 rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/95 shadow-[0_18px_60px_rgba(0,0,0,0.07)] overflow-hidden backdrop-blur">
            <div class="relative p-6 border-b border-outline-variant/60 overflow-hidden">
                <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full bg-primary-container/25 blur-3xl"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-label-sm font-bold mb-3">
                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                            AI Product Artist
                        </div>

                        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Produk Artist</h1>

                        <p class="mt-2 text-body-md text-on-surface-variant">
                            Gabungkan foto produk dan model/artis menjadi visual campaign premium.
                        </p>
                    </div>

                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container items-center justify-center shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">photo_camera</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-7">
                <!-- 1. Upload Section -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">1</span>
                            Upload Aset
                        </h2>

                        <span id="assetCounterBadge" class="px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold">
                            0/2 Siap
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Product Upload -->
                        <div>
                            <input id="productInput" class="hidden" type="file" accept="image/png,image/jpeg,image/jpg,image/webp">

                            <button
                                id="productDropzone"
                                class="artist-upload-zone relative group w-full aspect-[4/3] rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 hover:border-primary hover:bg-primary-container/10 transition-all cursor-pointer overflow-hidden"
                                type="button"
                                aria-label="Upload foto produk"
                            >
                                <img id="productPreview" class="hidden absolute inset-0 w-full h-full object-contain bg-white p-3" alt="Preview produk">

                                <div id="productPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-center p-4 pointer-events-none">
                                    <span class="w-14 h-14 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                                    </span>
                                    <span class="font-bold text-on-surface">Upload Produk</span>
                                    <span class="text-label-sm text-on-surface-variant mt-1">Klik atau drag foto</span>
                                </div>

                                <div id="productBadge" class="hidden absolute top-3 right-3 px-3 py-1 rounded-full bg-primary text-on-primary text-[11px] font-bold items-center gap-1 shadow-lg">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                                    Produk
                                </div>

                                <div id="productMeta" class="hidden absolute left-3 right-3 bottom-3 rounded-xl bg-white/90 backdrop-blur-md border border-white/70 px-3 py-2 text-left shadow-lg">
                                    <p id="productFileName" class="truncate text-[11px] font-bold text-on-surface">produk.jpg</p>
                                    <p class="text-[10px] text-on-surface-variant">Klik untuk ganti foto</p>
                                </div>
                            </button>

                            <button id="removeProductBtn" class="hidden mt-2 w-full py-2.5 rounded-xl border border-error text-error font-label-sm hover:bg-error-container transition-all" type="button">
                                Hapus Produk
                            </button>
                        </div>

                        <!-- Model Upload -->
                        <div>
                            <input id="modelInput" class="hidden" type="file" accept="image/png,image/jpeg,image/jpg,image/webp">

                            <button
                                id="modelDropzone"
                                class="artist-upload-zone relative group w-full aspect-[4/3] rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 hover:border-primary hover:bg-primary-container/10 transition-all cursor-pointer overflow-hidden"
                                type="button"
                                aria-label="Upload foto model atau artis"
                            >
                                <img id="modelPreview" class="hidden absolute inset-0 w-full h-full object-contain bg-white p-3" alt="Preview model atau artis">

                                <div id="modelPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-center p-4 pointer-events-none">
                                    <span class="w-14 h-14 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined text-3xl">person_add</span>
                                    </span>
                                    <span class="font-bold text-on-surface">Upload Model</span>
                                    <span class="text-label-sm text-on-surface-variant mt-1">Klik atau drag foto</span>
                                </div>

                                <div id="modelBadge" class="hidden absolute top-3 right-3 px-3 py-1 rounded-full bg-primary text-on-primary text-[11px] font-bold items-center gap-1 shadow-lg">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                                    Model
                                </div>

                                <div id="modelMeta" class="hidden absolute left-3 right-3 bottom-3 rounded-xl bg-white/90 backdrop-blur-md border border-white/70 px-3 py-2 text-left shadow-lg">
                                    <p id="modelFileName" class="truncate text-[11px] font-bold text-on-surface">model.jpg</p>
                                    <p class="text-[10px] text-on-surface-variant">Klik untuk ganti foto</p>
                                </div>
                            </button>

                            <button id="removeModelBtn" class="hidden mt-2 w-full py-2.5 rounded-xl border border-error text-error font-label-sm hover:bg-error-container transition-all" type="button">
                                Hapus Model
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 flex items-start gap-2">
                        <span id="uploadStatusIcon" class="material-symbols-outlined text-[18px] text-on-surface-variant mt-0.5">info</span>
                        <p id="uploadStatusText" class="text-label-sm text-on-surface-variant">
                            Upload foto produk dan model/artis terlebih dahulu.
                        </p>
                    </div>
                </section>

                <!-- 2. Logo Upload -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">2</span>
                            Tambah Logo
                        </h2>

                        <span class="px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold">Opsional</span>
                    </div>

                    <input id="logoInput" class="hidden" type="file" accept="image/png,image/jpeg,image/jpg,image/webp">

                    <button
                        id="logoDropzone"
                        class="artist-upload-zone relative group w-full min-h-28 rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 hover:border-primary hover:bg-primary-container/10 transition-all cursor-pointer overflow-hidden"
                        type="button"
                        aria-label="Upload logo brand"
                    >
                        <div class="flex items-center gap-4 p-4 text-left">
                            <div class="relative w-20 h-20 shrink-0 rounded-2xl bg-white border border-outline-variant/60 overflow-hidden flex items-center justify-center">
                                <img id="logoPreview" class="hidden absolute inset-0 w-full h-full object-contain p-2" alt="Preview logo">
                                <span id="logoPlaceholder" class="material-symbols-outlined text-3xl text-primary">add_photo_alternate</span>
                            </div>

                            <div class="min-w-0">
                                <p id="logoTitle" class="font-bold text-on-surface">Upload Logo Brand</p>
                                <p id="logoFileName" class="mt-1 text-label-sm text-on-surface-variant truncate">Klik atau drag logo ke area ini</p>
                                <p class="mt-1 text-[11px] text-on-surface-variant">PNG transparan direkomendasikan</p>
                            </div>
                        </div>
                    </button>

                    <button id="removeLogoBtn" class="hidden mt-2 w-full py-2.5 rounded-xl border border-error text-error font-label-sm hover:bg-error-container transition-all" type="button">
                        Hapus Logo
                    </button>
                </section>

                <!-- 3. Prompt Section -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">3</span>
                            Prompt Campaign
                        </h2>

                        <button id="enhancePromptBtn" class="text-primary flex items-center gap-1 hover:underline text-label-sm font-bold" type="button">
                            <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                            Enhance Prompt
                        </button>
                    </div>

                    <div class="relative">
                        <textarea
                            id="promptInput"
                            maxlength="700"
                            class="w-full h-36 p-4 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md resize-none outline-none placeholder:text-on-surface-variant/60"
                            placeholder="Contoh: luxury studio campaign, soft teal lighting, produk tajam, model memegang produk secara natural, background premium, commercial photography."
                        ></textarea>

                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-3">
                            <span id="promptCounter" class="px-2 py-1 rounded-full bg-surface-container-lowest text-[11px] text-on-surface-variant border border-outline-variant/60">0/700</span>
                            <span class="px-2 py-1 rounded-full bg-surface-container-lowest text-[11px] text-on-surface-variant border border-outline-variant/60">Opsional</span>
                        </div>
                    </div>
                </section>

                <!-- 4. Ratio Selection -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">4</span>
                            Pilih Rasio
                        </h2>

                        <span id="ratioHint" class="text-label-sm text-on-surface-variant font-medium">Square</span>
                    </div>

                    <div id="ratioGroup" class="grid grid-cols-5 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-ratio="1:1" data-label="Square" class="ratio-button rounded-xl py-2.5 px-2 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-md transition-all">1:1</button>
                        <button type="button" data-ratio="4:5" data-label="Portrait" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">4:5</button>
                        <button type="button" data-ratio="9:16" data-label="Story" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">9:16</button>
                        <button type="button" data-ratio="16:9" data-label="Wide" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">16:9</button>
                        <button type="button" data-ratio="3:2" data-label="Photo" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">3:2</button>
                    </div>
                </section>

                <!-- 5. Result Count -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">5</span>
                            Jumlah Hasil
                        </h2>

                        <span class="text-label-sm text-on-surface-variant font-medium">
                            Estimasi: <span id="creditEstimate" class="text-primary font-bold">4 Kredit</span>
                        </span>
                    </div>

                    <div id="countGroup" class="grid grid-cols-2 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-count="4" class="count-button rounded-xl py-2.5 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-md transition-all">
                            Biasa <span class="text-[11px] opacity-75">(4 hasil)</span>
                        </button>
                        <button type="button" data-count="10" class="count-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">
                            Booster <span class="text-[11px] opacity-75">(10 hasil)</span>
                        </button>
                    </div>
                </section>

                <!-- CTA -->
                <button id="generateBtn" class="relative w-full overflow-hidden py-4 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl font-headline-md text-headline-md flex items-center justify-center gap-3 hover:brightness-105 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none" type="button">
                    <span class="absolute inset-0 artist-shine opacity-40"></span>
                    <span id="generateIcon" class="material-symbols-outlined relative" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                    <span id="generateText" class="relative">Upload Produk & Model Dulu</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- RIGHT PANEL -->
    <main class="xl:col-span-7 2xl:col-span-8 min-w-0">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-label-sm font-bold mb-3">
                    <span class="material-symbols-outlined text-[16px]">campaign</span>
                    Preview Campaign
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span id="outputRatioBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">Rasio 1:1</span>
                <span id="outputCountBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">4 Hasil</span>
            </div>
        </div>

        <section class="rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/80 shadow-[0_18px_60px_rgba(0,0,0,0.06)] p-4 sm:p-6 min-h-[560px] backdrop-blur">
            <div id="emptyResults" class="min-h-[510px] rounded-3xl border-2 border-dashed border-outline-variant/80 bg-gradient-to-br from-surface-container-lowest via-surface-container-low to-primary-container/10 flex flex-col items-center justify-center text-center p-8 overflow-hidden relative">
                <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-primary-container/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-56 h-56 rounded-full bg-primary/10 blur-3xl"></div>

                <div class="relative w-20 h-20 rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-lg flex items-center justify-center mb-5">
                    <span id="emptyIcon" class="material-symbols-outlined text-5xl text-primary">photo_camera</span>
                </div>

                <h3 id="emptyTitle" class="relative font-headline-md text-on-surface mb-2">Belum ada hasil generate</h3>

                <p id="emptyDescription" class="relative text-body-md text-on-surface-variant max-w-md leading-relaxed">
                    Upload foto produk dan model/artis, isi prompt campaign, pilih rasio dan jumlah hasil, lalu klik Generate Product Artist.
                </p>

                <div class="relative mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Produk wajib</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Model wajib</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Tanpa contoh palsu</span>
                </div>
            </div>

            <div id="resultsGrid" class="hidden grid grid-cols-1 md:grid-cols-2 gap-5"></div>
        </section>
    </main>

    <!-- Toast -->
    <div id="toast" class="hidden fixed right-6 bottom-6 z-[80] max-w-sm rounded-2xl bg-inverse-surface text-inverse-on-surface px-5 py-4 shadow-2xl">
        <div class="flex items-start gap-3">
            <span id="toastIcon" class="material-symbols-outlined text-primary-fixed-dim">info</span>
            <p id="toastText" class="text-label-md font-medium">Pesan</p>
        </div>
    </div>
</div>

<style>
    .artist-upload-zone.is-dragging {
        border-color: #20c4d8;
        background: rgba(32, 196, 216, 0.08);
        transform: translateY(-1px);
    }

    .artist-upload-zone.is-ready {
        border-style: solid;
        border-color: rgba(0, 104, 116, 0.45);
        background: #ffffff;
    }

    .prompt-chip {
        border-radius: 9999px;
        background: #ffffff;
        border: 1px solid #bbc9cc;
        padding: 0.45rem 0.8rem;
        font-size: 12px;
        font-weight: 800;
        color: #3c494b;
        transition: all .2s ease;
    }

    .prompt-chip:hover {
        border-color: #006874;
        color: #006874;
        background: rgba(32, 196, 216, 0.08);
    }

    .artist-shine::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 45%;
        height: 320%;
        transform: rotate(25deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
        animation: artistShine 3.8s ease-in-out infinite;
    }

    @keyframes artistShine {
        0%, 45% { left: -50%; }
        100% { left: 130%; }
    }

    .skeleton-shimmer {
        position: relative;
        overflow: hidden;
        background: #e6e8ea;
    }

    .skeleton-shimmer::after {
        content: "";
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.55), transparent);
        animation: artistShimmer 1.25s infinite;
    }

    @keyframes artistShimmer {
        100% { transform: translateX(100%); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const CREDITS_PER_RESULT = 1;
    const MAX_PROMPT_LENGTH = 700;
    const MAX_ASSET_SIZE = 10 * 1024 * 1024;
    const MAX_LOGO_SIZE = 5 * 1024 * 1024;
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    const GENERATE_ENDPOINT = @json(url('/artist/generate'));

    const state = {
        productFile: null,
        modelFile: null,
        logoFile: null,
        productPreviewUrl: null,
        modelPreviewUrl: null,
        logoPreviewUrl: null,
        ratio: '1:1',
        ratioLabel: 'Square',
        count: 4,
        isGenerating: false,
    };

    const elements = {
        productInput: document.getElementById('productInput'),
        productDropzone: document.getElementById('productDropzone'),
        productPreview: document.getElementById('productPreview'),
        productPlaceholder: document.getElementById('productPlaceholder'),
        productBadge: document.getElementById('productBadge'),
        productMeta: document.getElementById('productMeta'),
        productFileName: document.getElementById('productFileName'),
        removeProductBtn: document.getElementById('removeProductBtn'),

        modelInput: document.getElementById('modelInput'),
        modelDropzone: document.getElementById('modelDropzone'),
        modelPreview: document.getElementById('modelPreview'),
        modelPlaceholder: document.getElementById('modelPlaceholder'),
        modelBadge: document.getElementById('modelBadge'),
        modelMeta: document.getElementById('modelMeta'),
        modelFileName: document.getElementById('modelFileName'),
        removeModelBtn: document.getElementById('removeModelBtn'),

        logoInput: document.getElementById('logoInput'),
        logoDropzone: document.getElementById('logoDropzone'),
        logoPreview: document.getElementById('logoPreview'),
        logoPlaceholder: document.getElementById('logoPlaceholder'),
        logoTitle: document.getElementById('logoTitle'),
        logoFileName: document.getElementById('logoFileName'),
        removeLogoBtn: document.getElementById('removeLogoBtn'),

        assetCounterBadge: document.getElementById('assetCounterBadge'),
        uploadStatusIcon: document.getElementById('uploadStatusIcon'),
        uploadStatusText: document.getElementById('uploadStatusText'),

        promptInput: document.getElementById('promptInput'),
        promptCounter: document.getElementById('promptCounter'),
        enhancePromptBtn: document.getElementById('enhancePromptBtn'),

        ratioButtons: document.querySelectorAll('.ratio-button'),
        countButtons: document.querySelectorAll('.count-button'),
        ratioHint: document.getElementById('ratioHint'),
        creditEstimate: document.getElementById('creditEstimate'),
        outputRatioBadge: document.getElementById('outputRatioBadge'),
        outputCountBadge: document.getElementById('outputCountBadge'),
        resultsCountBadge: document.getElementById('outputCountBadge'),

        generateBtn: document.getElementById('generateBtn'),
        generateIcon: document.getElementById('generateIcon'),
        generateText: document.getElementById('generateText'),

        resultsSubtitle: document.getElementById('resultsSubtitle'),
        emptyResults: document.getElementById('emptyResults'),
        emptyIcon: document.getElementById('emptyIcon'),
        emptyTitle: document.getElementById('emptyTitle'),
        emptyDescription: document.getElementById('emptyDescription'),
        resultsGrid: document.getElementById('resultsGrid'),

        toast: document.getElementById('toast'),
        toastIcon: document.getElementById('toastIcon'),
        toastText: document.getElementById('toastText'),
    };

    const activeClasses = ['bg-primary-container', 'text-on-primary-container', 'shadow-sm'];
    const inactiveClasses = ['text-on-surface-variant', 'hover:bg-surface-container-high'];

    let toastTimer = null;

    function showToast(message, type = 'info') {
        clearTimeout(toastTimer);

        const iconMap = {
            info: 'info',
            success: 'check_circle',
            error: 'error',
        };

        elements.toastIcon.textContent = iconMap[type] || 'info';
        elements.toastText.textContent = message;
        elements.toast.classList.remove('hidden');

        toastTimer = setTimeout(() => {
            elements.toast.classList.add('hidden');
        }, 3200);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 KB';

        const units = ['B', 'KB', 'MB', 'GB'];
        const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        const size = bytes / Math.pow(1024, index);

        return `${size.toFixed(index === 0 ? 0 : 1)} ${units[index]}`;
    }

    function getAspectRatioValue() {
        const [width, height] = state.ratio.split(':').map(Number);
        return `${width} / ${height}`;
    }

    function setButtonActive(buttons, activeButton) {
        buttons.forEach((button) => {
            button.classList.remove(...activeClasses, ...inactiveClasses);
            button.setAttribute('aria-pressed', button === activeButton ? 'true' : 'false');

            if (button === activeButton) {
                button.classList.add(...activeClasses);
            } else {
                button.classList.add(...inactiveClasses);
            }
        });
    }

    function updatePromptCounter() {
        elements.promptCounter.textContent = `${elements.promptInput.value.length}/${MAX_PROMPT_LENGTH}`;
    }

    function updateOutputMeta() {
        const modeLabel = state.count === 10 ? 'Booster' : 'Biasa';
        elements.ratioHint.textContent = state.ratioLabel;
        elements.creditEstimate.textContent = `${state.count * CREDITS_PER_RESULT} Kredit`;
        elements.outputRatioBadge.textContent = `Rasio ${state.ratio}`;
        elements.outputCountBadge.textContent = `${modeLabel} · ${state.count} Hasil`;
    }

    function showUploadMessage(message, type = 'info') {
        elements.uploadStatusText.textContent = message;
        elements.uploadStatusText.className = 'text-label-sm';
        elements.uploadStatusIcon.className = 'material-symbols-outlined text-[18px] mt-0.5';

        if (type === 'error') {
            elements.uploadStatusText.classList.add('text-red-600');
            elements.uploadStatusIcon.classList.add('text-red-600');
            elements.uploadStatusIcon.textContent = 'error';
            return;
        }

        if (type === 'success') {
            elements.uploadStatusText.classList.add('text-primary');
            elements.uploadStatusIcon.classList.add('text-primary');
            elements.uploadStatusIcon.textContent = 'check_circle';
            return;
        }

        elements.uploadStatusText.classList.add('text-on-surface-variant');
        elements.uploadStatusIcon.classList.add('text-on-surface-variant');
        elements.uploadStatusIcon.textContent = 'info';
    }

    function refreshFormState() {
        const readyCount = Number(Boolean(state.productFile)) + Number(Boolean(state.modelFile));
        const isReady = readyCount === 2;

        elements.assetCounterBadge.textContent = `${readyCount}/2 Siap`;
        elements.assetCounterBadge.className = isReady
            ? 'px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-sm font-bold'
            : 'px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold';

        if (readyCount === 0) {
            showUploadMessage('Upload foto produk dan model/artis terlebih dahulu.');
        } else if (readyCount === 1) {
            showUploadMessage(state.productFile ? 'Produk sudah siap. Upload model/artis untuk melanjutkan.' : 'Model/artis sudah siap. Upload produk untuk melanjutkan.', 'error');
        } else {
            showUploadMessage('Produk dan model/artis siap digunakan untuk campaign.', 'success');
        }

        if (!state.isGenerating) {
            elements.generateBtn.disabled = !isReady;
            elements.generateText.textContent = isReady ? 'Generate Product Artist' : 'Upload Produk & Model Dulu';
        }
    }

    function setUploadPreview(type, file) {
        if (!file) return;

        if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
            showToast('File harus berupa gambar PNG, JPG, JPEG, atau WEBP.', 'error');
            return;
        }

        if (file.size > MAX_ASSET_SIZE) {
            showToast('Ukuran foto maksimal 10 MB.', 'error');
            return;
        }

        const isProduct = type === 'product';
        const previousUrl = isProduct ? state.productPreviewUrl : state.modelPreviewUrl;

        if (previousUrl) URL.revokeObjectURL(previousUrl);

        const previewUrl = URL.createObjectURL(file);

        if (isProduct) {
            state.productFile = file;
            state.productPreviewUrl = previewUrl;
        } else {
            state.modelFile = file;
            state.modelPreviewUrl = previewUrl;
        }

        const preview = isProduct ? elements.productPreview : elements.modelPreview;
        const placeholder = isProduct ? elements.productPlaceholder : elements.modelPlaceholder;
        const badge = isProduct ? elements.productBadge : elements.modelBadge;
        const meta = isProduct ? elements.productMeta : elements.modelMeta;
        const fileName = isProduct ? elements.productFileName : elements.modelFileName;
        const dropzone = isProduct ? elements.productDropzone : elements.modelDropzone;
        const removeBtn = isProduct ? elements.removeProductBtn : elements.removeModelBtn;

        preview.src = previewUrl;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        badge.classList.remove('hidden');
        badge.classList.add('flex');
        meta.classList.remove('hidden');
        fileName.textContent = `${file.name} • ${formatFileSize(file.size)}`;
        dropzone.classList.add('is-ready');
        removeBtn.classList.remove('hidden');

        showToast(isProduct ? 'Foto produk berhasil dimuat.' : 'Foto model/artis berhasil dimuat.', 'success');
        refreshFormState();
    }

    function clearUpload(type) {
        const isProduct = type === 'product';
        const previewUrl = isProduct ? state.productPreviewUrl : state.modelPreviewUrl;

        if (previewUrl) URL.revokeObjectURL(previewUrl);

        if (isProduct) {
            state.productFile = null;
            state.productPreviewUrl = null;
            elements.productInput.value = '';
        } else {
            state.modelFile = null;
            state.modelPreviewUrl = null;
            elements.modelInput.value = '';
        }

        const preview = isProduct ? elements.productPreview : elements.modelPreview;
        const placeholder = isProduct ? elements.productPlaceholder : elements.modelPlaceholder;
        const badge = isProduct ? elements.productBadge : elements.modelBadge;
        const meta = isProduct ? elements.productMeta : elements.modelMeta;
        const dropzone = isProduct ? elements.productDropzone : elements.modelDropzone;
        const removeBtn = isProduct ? elements.removeProductBtn : elements.removeModelBtn;

        preview.removeAttribute('src');
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        badge.classList.add('hidden');
        badge.classList.remove('flex');
        meta.classList.add('hidden');
        dropzone.classList.remove('is-ready');
        removeBtn.classList.add('hidden');

        showToast(isProduct ? 'Foto produk dihapus.' : 'Foto model/artis dihapus.', 'info');
        refreshFormState();
    }

    function setLogoPreview(file) {
        if (!file) return;

        if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
            showToast('Logo harus berupa gambar PNG, JPG, JPEG, atau WEBP.', 'error');
            return;
        }

        if (file.size > MAX_LOGO_SIZE) {
            showToast('Ukuran logo maksimal 5 MB.', 'error');
            return;
        }

        if (state.logoPreviewUrl) URL.revokeObjectURL(state.logoPreviewUrl);

        state.logoFile = file;
        state.logoPreviewUrl = URL.createObjectURL(file);
        elements.logoPreview.src = state.logoPreviewUrl;
        elements.logoPreview.classList.remove('hidden');
        elements.logoPlaceholder.classList.add('hidden');
        elements.logoTitle.textContent = 'Logo Siap Digunakan';
        elements.logoFileName.textContent = `${file.name} • ${formatFileSize(file.size)}`;
        elements.logoDropzone.classList.add('is-ready');
        elements.removeLogoBtn.classList.remove('hidden');

        showToast('Logo berhasil dimuat.', 'success');
    }

    function clearLogo() {
        if (state.logoPreviewUrl) URL.revokeObjectURL(state.logoPreviewUrl);

        state.logoFile = null;
        state.logoPreviewUrl = null;
        elements.logoInput.value = '';
        elements.logoPreview.removeAttribute('src');
        elements.logoPreview.classList.add('hidden');
        elements.logoPlaceholder.classList.remove('hidden');
        elements.logoTitle.textContent = 'Upload Logo Brand';
        elements.logoFileName.textContent = 'Klik atau drag logo ke area ini';
        elements.logoDropzone.classList.remove('is-ready');
        elements.removeLogoBtn.classList.add('hidden');

        showToast('Logo dihapus.', 'info');
    }

    function setupDropzone(type, dropzone, input) {
        dropzone.addEventListener('click', () => {
            if (!state.isGenerating) input.click();
        });

        input.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            setUploadPreview(type, file);
            input.value = '';
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'dragend'].forEach((eventName) => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.remove('is-dragging');
            });
        });

        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('is-dragging');
            const file = event.dataTransfer.files?.[0];
            setUploadPreview(type, file);
        });
    }

    function setupLogoDropzone() {
        elements.logoDropzone.addEventListener('click', () => {
            if (!state.isGenerating) elements.logoInput.click();
        });

        elements.logoInput.addEventListener('change', (event) => {
            setLogoPreview(event.target.files?.[0]);
            elements.logoInput.value = '';
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            elements.logoDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                elements.logoDropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'dragend'].forEach((eventName) => {
            elements.logoDropzone.addEventListener(eventName, () => {
                elements.logoDropzone.classList.remove('is-dragging');
            });
        });

        elements.logoDropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            elements.logoDropzone.classList.remove('is-dragging');
            setLogoPreview(event.dataTransfer.files?.[0]);
        });
    }

    function showEmptyState(title, description, icon = 'photo_camera') {
        elements.emptyTitle.textContent = title;
        elements.emptyDescription.textContent = description;
        elements.emptyIcon.textContent = icon;
        elements.emptyResults.classList.remove('hidden');
        elements.resultsGrid.classList.add('hidden');
        elements.resultsGrid.innerHTML = '';
        elements.resultsCountBadge.textContent = '0 Hasil';
        elements.resultsSubtitle.textContent = 'Hasil campaign hanya muncul setelah proses generate berhasil.';
    }

    function showLoadingState() {
        elements.emptyResults.classList.add('hidden');
        elements.resultsGrid.classList.remove('hidden');
        elements.resultsGrid.innerHTML = '';
        elements.resultsCountBadge.textContent = `${state.count} Diproses`;
        elements.resultsSubtitle.textContent = 'AI sedang membuat visual campaign premium. Tunggu sebentar.';

        for (let index = 0; index < state.count; index += 1) {
            const card = document.createElement('div');
            card.className = 'rounded-3xl border border-outline-variant bg-surface-container-lowest overflow-hidden shadow-sm animate-pulse';
            card.style.aspectRatio = getAspectRatioValue();
            card.innerHTML = `
                <div class="w-full h-full bg-gradient-to-br from-surface-container-low via-surface-container-high to-surface-container-low flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full border-4 border-primary/20 border-t-primary animate-spin mx-auto"></div>
                        <p class="mt-3 text-label-md font-bold text-on-surface-variant">Membuat campaign ${index + 1}</p>
                    </div>
                </div>
            `;
            elements.resultsGrid.appendChild(card);
        }
    }

    function setFormLocked(isLocked) {
        [
            elements.productDropzone,
            elements.modelDropzone,
            elements.logoDropzone,
            elements.removeProductBtn,
            elements.removeModelBtn,
            elements.removeLogoBtn,
            elements.promptInput,
            elements.enhancePromptBtn,
            ...elements.ratioButtons,
            ...elements.countButtons,
        ].forEach((element) => {
            if (!element) return;
            element.disabled = isLocked;
        });
    }

    function normalizeResults(data) {
        if (!data) return [];

        const candidateArrays = [
            data.images,
            data.results,
            data.outputs,
            data.urls,
            data.data?.images,
            data.data?.results,
            data.data?.outputs,
            Array.isArray(data.data) ? data.data : null,
            Array.isArray(data) ? data : null,
        ].filter(Boolean);

        let rawResults = candidateArrays.find(Array.isArray) || [];

        if (!rawResults.length) {
            const singleImage = data.image_url || data.image || data.url || data.output_url || data.output;
            rawResults = singleImage ? [singleImage] : [];
        }

        return rawResults
            .map((item, index) => {
                if (typeof item === 'string') {
                    return {
                        imageUrl: item,
                        title: `Campaign ${index + 1}`,
                        meta: `Product Artist • ${state.ratio}`,
                    };
                }

                return {
                    imageUrl: item?.url || item?.image || item?.src || item?.image_url || item?.output_url || item?.output || '',
                    title: item?.title || item?.name || `Campaign ${index + 1}`,
                    meta: item?.meta || item?.subtitle || `Product Artist • ${state.ratio}`,
                };
            })
            .filter((item) => Boolean(item.imageUrl))
            .slice(0, state.count);
    }

    function makeOverlayAction(icon, label, href = null, download = false) {
        const element = href ? document.createElement('a') : document.createElement('button');
        element.className = 'w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 hover:scale-105 transition-all';
        element.setAttribute('aria-label', label);

        if (href) {
            element.href = href;
            if (download) {
                element.download = '';
            } else {
                element.target = '_blank';
                element.rel = 'noopener';
            }
        } else {
            element.type = 'button';
        }

        element.innerHTML = `<span class="material-symbols-outlined">${icon}</span>`;
        return element;
    }

    function createResultCard(item, index) {
        const card = document.createElement('article');
        card.className = 'bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0_14px_40px_rgba(0,0,0,0.08)] border border-outline-variant group relative';
        card.style.aspectRatio = getAspectRatioValue();

        const image = document.createElement('img');
        image.className = 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105';
        image.alt = item.title || `Campaign ${index + 1}`;
        image.src = item.imageUrl;
        image.loading = 'lazy';
        image.addEventListener('error', () => {
            image.remove();
            const errorState = document.createElement('div');
            errorState.className = 'flex h-full w-full flex-col items-center justify-center bg-surface-container-low p-6 text-center text-on-surface-variant';

            const errorIcon = document.createElement('span');
            errorIcon.className = 'material-symbols-outlined text-4xl text-error';
            errorIcon.textContent = 'broken_image';

            const errorText = document.createElement('p');
            errorText.className = 'mt-3 text-label-md font-bold';
            errorText.textContent = 'Gambar gagal dimuat';

            const originalLink = document.createElement('a');
            originalLink.href = item.imageUrl;
            originalLink.target = '_blank';
            originalLink.rel = 'noopener';
            originalLink.className = 'mt-2 text-label-sm font-bold text-primary hover:underline';
            originalLink.textContent = 'Buka URL asli';

            errorState.append(errorIcon, errorText, originalLink);
            card.prepend(errorState);
        }, { once: true });

        const badge = document.createElement('div');
        badge.className = 'absolute top-3 left-3 px-3 py-1 rounded-full bg-black/50 text-white text-[11px] font-bold backdrop-blur';
        badge.textContent = `Campaign ${index + 1} · ${state.ratio}`;

        const overlay = document.createElement('div');
        overlay.className = 'absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4';

        const actionRow = document.createElement('div');
        actionRow.className = 'flex justify-between items-center gap-3';

        const leftActions = document.createElement('div');
        leftActions.className = 'flex gap-2';
        leftActions.appendChild(makeOverlayAction('download', 'Download hasil', item.imageUrl, true));
        leftActions.appendChild(makeOverlayAction('zoom_in', 'Lihat hasil', item.imageUrl));

        const rightActions = document.createElement('div');
        rightActions.className = 'flex gap-2';

        const favoriteButton = makeOverlayAction('favorite', 'Favorit');
        favoriteButton.addEventListener('click', () => {
            favoriteButton.classList.toggle('bg-white/40');
            favoriteButton.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' 1";
        });

        rightActions.appendChild(favoriteButton);

        actionRow.appendChild(leftActions);
        actionRow.appendChild(rightActions);

        overlay.appendChild(actionRow);
        card.appendChild(image);
        card.appendChild(badge);
        card.appendChild(overlay);

        return card;
    }

    function renderResults(results) {
        const validResults = results.slice(0, state.count);

        if (!validResults.length) {
            showEmptyState('Belum ada URL gambar dari backend', 'Proses selesai, tetapi server belum mengirim data gambar hasil. Pastikan response JSON berisi images/results.', 'image_not_supported');
            showToast('Backend belum mengirim URL gambar hasil.', 'info');
            return;
        }

        elements.emptyResults.classList.add('hidden');
        elements.resultsGrid.classList.remove('hidden');
        elements.resultsGrid.innerHTML = '';
        elements.resultsCountBadge.textContent = `${validResults.length} Hasil`;
        elements.resultsSubtitle.textContent = 'Visual campaign berhasil dibuat. Kamu bisa membuka atau download hasilnya.';

        validResults.forEach((item, index) => {
            elements.resultsGrid.appendChild(createResultCard(item, index));
        });

        showToast('Campaign berhasil dibuat.', 'success');
    }

    async function requestGenerate() {
        const formData = new FormData();

        formData.append('product_image', state.productFile);
        formData.append('model_image', state.modelFile);
        if (state.logoFile) formData.append('logo_image', state.logoFile);
        formData.append('prompt', elements.promptInput.value.trim());
        formData.append('ratio', state.ratio);
        formData.append('count', String(state.count));

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(GENERATE_ENDPOINT, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationMessage = data.errors
                ? Object.values(data.errors).flat().find(Boolean)
                : null;
            throw new Error(validationMessage || data.message || `Generate gagal dengan status ${response.status}.`);
        }

        return data;
    }

    async function handleGenerate() {
        if (!state.productFile || !state.modelFile) {
            showToast('Upload foto produk dan model/artis terlebih dahulu.', 'error');
            return;
        }

        state.isGenerating = true;
        setFormLocked(true);
        elements.generateBtn.disabled = true;
        elements.generateBtn.classList.add('opacity-70', 'cursor-not-allowed');
        elements.generateIcon.textContent = 'progress_activity';
        elements.generateIcon.classList.add('animate-spin');
        elements.generateText.textContent = 'Memproses Campaign...';
        showLoadingState();

        try {
            const data = await requestGenerate();
            renderResults(normalizeResults(data));
        } catch (error) {
            showEmptyState('Generate belum berhasil', error.message || 'Terjadi kesalahan saat membuat visual campaign.', 'warning');
            showToast(error.message || 'Generate gagal. Silakan coba kembali.', 'error');
        } finally {
            state.isGenerating = false;
            setFormLocked(false);
            elements.generateBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            elements.generateIcon.textContent = 'auto_awesome';
            elements.generateIcon.classList.remove('animate-spin');
            refreshFormState();
        }
    }

    setupDropzone('product', elements.productDropzone, elements.productInput);
    setupDropzone('model', elements.modelDropzone, elements.modelInput);
    setupLogoDropzone();

    elements.removeProductBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        clearUpload('product');
    });

    elements.removeModelBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        clearUpload('model');
    });

    elements.removeLogoBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        clearLogo();
    });

    elements.ratioButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.ratio = button.dataset.ratio;
            state.ratioLabel = button.dataset.label || button.dataset.ratio;
            setButtonActive(elements.ratioButtons, button);
            updateOutputMeta();
        });
    });

    elements.countButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.count = Number(button.dataset.count);
            setButtonActive(elements.countButtons, button);
            updateOutputMeta();
        });
    });

    document.querySelectorAll('.prompt-chip').forEach((button) => {
        button.addEventListener('click', () => {
            const prompt = button.dataset.prompt;
            const current = elements.promptInput.value.trim();
            elements.promptInput.value = current ? `${current}\n\n${prompt}` : prompt;
            elements.promptInput.dispatchEvent(new Event('input'));
            elements.promptInput.focus();
        });
    });

    elements.enhancePromptBtn.addEventListener('click', () => {
        const currentPrompt = elements.promptInput.value.trim();
        const enhancedPrompt = 'Buat visual campaign produk yang mewah, realistis, dan profesional. Model/artis terlihat natural saat menampilkan produk, produk tetap tajam dan jelas, pencahayaan studio premium, background elegan, komposisi iklan modern, warna bersih, high-end commercial photography.';

        elements.promptInput.value = currentPrompt
            ? `${currentPrompt}\n\n${enhancedPrompt}`
            : enhancedPrompt;

        elements.promptInput.dispatchEvent(new Event('input'));
        elements.promptInput.focus();
        showToast('Prompt sudah ditingkatkan.', 'success');
    });

    elements.promptInput.addEventListener('input', updatePromptCounter);
    elements.generateBtn.addEventListener('click', handleGenerate);

    window.addEventListener('beforeunload', () => {
        if (state.productPreviewUrl) URL.revokeObjectURL(state.productPreviewUrl);
        if (state.modelPreviewUrl) URL.revokeObjectURL(state.modelPreviewUrl);
        if (state.logoPreviewUrl) URL.revokeObjectURL(state.logoPreviewUrl);
    });

    updatePromptCounter();
    setButtonActive(elements.ratioButtons, document.querySelector('.ratio-button[data-ratio="1:1"]'));
    setButtonActive(elements.countButtons, document.querySelector('.count-button[data-count="4"]'));
    updateOutputMeta();
    refreshFormState();
    showEmptyState('Belum ada hasil generate', 'Upload foto produk dan model/artis, isi prompt campaign, pilih rasio dan jumlah hasil, lalu klik Generate Product Artist.');
});
</script>
@endsection
