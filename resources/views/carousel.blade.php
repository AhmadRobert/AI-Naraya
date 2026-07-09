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
                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">view_carousel</span>
                            AI Carousel Renderer
                        </div>

                        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Carousel Render</h1>
                        <p class="mt-2 text-body-md text-on-surface-variant">
                            Upload storyboard PDF, pilih nomor reel, tambahkan referensi karakter, lalu render output visual.
                        </p>
                    </div>

                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container items-center justify-center shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">movie_creation</span>
                    </div>
                </div>

                <div class="relative mt-5 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-outline-variant/60 bg-surface-container-lowest/80 px-3 py-3 shadow-sm">
                        <p id="statusPdf" class="text-label-sm font-extrabold text-on-surface">PDF</p>
                        <p class="mt-0.5 text-[11px] font-semibold text-on-surface-variant">Belum</p>
                    </div>
                    <div class="rounded-2xl border border-outline-variant/60 bg-surface-container-lowest/80 px-3 py-3 shadow-sm">
                        <p id="statusReference" class="text-label-sm font-extrabold text-on-surface">0 Foto</p>
                        <p class="mt-0.5 text-[11px] font-semibold text-on-surface-variant">Referensi</p>
                    </div>
                    <div class="rounded-2xl border border-outline-variant/60 bg-surface-container-lowest/80 px-3 py-3 shadow-sm">
                        <p id="statusReel" class="text-label-sm font-extrabold text-on-surface">- Reel</p>
                        <p class="mt-0.5 text-[11px] font-semibold text-on-surface-variant">Terdeteksi</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-7">
                <!-- 1. PDF Upload -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">1</span>
                            Upload PDF Storyboard
                        </h2>

                        <span id="pdfBadge" class="hidden px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-sm font-bold">
                            PDF Siap
                        </span>
                    </div>

                    <input id="pdfInput" type="file" accept="application/pdf,.pdf" class="hidden">

                    <button
                        id="pdfDropZone"
                        type="button"
                        class="upload-zone group relative w-full min-h-[164px] overflow-hidden rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 p-5 text-left transition-all hover:border-primary hover:bg-primary/5"
                        aria-label="Upload PDF storyboard"
                    >
                        <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-primary-container/20 blur-3xl transition-transform group-hover:scale-125"></div>

                        <div class="relative flex h-full items-center gap-4">
                            <div id="pdfIconWrap" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/50">
                                <span id="pdfIcon" class="material-symbols-outlined text-[36px]">picture_as_pdf</span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p id="pdfUploadText" class="font-bold text-on-surface">Klik atau drag PDF ke sini</p>
                                <p id="pdfFileName" class="mt-1 hidden truncate text-label-sm font-bold text-primary"></p>
                                <p id="pdfInfo" class="mt-1 text-label-sm leading-relaxed text-on-surface-variant">
                                    Sistem akan mendeteksi jumlah reel dari penomoran ide/reel di nama file dan isi PDF.
                                </p>
                            </div>
                        </div>
                    </button>
                </section>

                <!-- 2. Reference Photos -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">2</span>
                            Foto Referensi
                        </h2>

                        <button id="clearReferenceBtn" type="button" class="hidden text-label-sm font-bold text-error hover:underline">
                            Hapus semua
                        </button>
                    </div>

                    <input id="referenceInput" type="file" accept="image/*" multiple class="hidden">

                    <button
                        id="referenceDropZone"
                        type="button"
                        class="upload-zone group relative min-h-[172px] w-full overflow-hidden rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 p-4 transition-all hover:border-primary hover:bg-primary/5"
                        aria-label="Upload foto referensi"
                    >
                        <div id="referenceEmptyState" class="flex min-h-[140px] flex-col items-center justify-center text-center p-5">
                            <span class="w-14 h-14 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                            </span>
                            <span class="font-bold text-on-surface">Klik atau drag foto referensi</span>
                            <span class="text-label-sm text-on-surface-variant mt-1">Bisa upload lebih dari satu foto karakter/model.</span>
                        </div>

                        <div id="referenceLoadedState" class="hidden min-h-[140px] rounded-2xl border border-[#00875A]/30 bg-[#EEF9F2] p-5 text-center">
                            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-white text-[#00875A] shadow-sm">
                                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
                            </div>

                            <p class="font-extrabold text-[#007A5A]">Karakter Dimuat ✓</p>
                            <p id="referenceLoadedText" class="mt-1 text-label-sm text-on-surface-variant">0 foto referensi siap dipakai</p>
                            <div id="referenceFileList" class="mt-4 flex flex-wrap justify-center gap-2"></div>
                        </div>
                    </button>
                </section>

                <!-- 3. Reel Selection -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">3</span>
                            Pilih Nomor Reel
                        </h2>

                        <span id="reelCountBadge" class="px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold">
                            Upload PDF dulu
                        </span>
                    </div>

                    <div class="relative">
                        <select
                            id="reelSelect"
                            disabled
                            class="reel-select-clean w-full rounded-2xl border border-outline-variant bg-surface-container-lowest px-4 py-4 pr-12 font-label-md text-on-surface outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <option value="">Upload PDF storyboard dulu</option>
                        </select>

                        <span class="material-symbols-outlined pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant">
                            keyboard_arrow_down
                        </span>
                    </div>

                </section>

                <!-- 4. Extra Prompt -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">4</span>
                            Kondisi Tambahan
                        </h2>

                        <span id="promptCounter" class="text-[11px] font-bold text-on-surface-variant">0/700</span>
                    </div>

                    <div class="relative">
                        <textarea
                            id="extraPrompt"
                            maxlength="700"
                            class="w-full h-32 p-4 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md resize-none outline-none placeholder:text-on-surface-variant/60"
                            placeholder="Contoh: gaya cinematic, warna hangat, karakter terlihat natural, background clean, detail produk tetap jelas..."
                        ></textarea>
                    </div>
                </section>

                <!-- 5. Aspect Ratio -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">5</span>
                            Aspect Ratio
                        </h2>

                        <span id="ratioHint" class="text-label-sm text-on-surface-variant font-medium">Landscape</span>
                    </div>

                    <div id="aspectRatioGroup" class="grid grid-cols-5 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-ratio="16:9" data-label="Landscape" class="aspect-btn rounded-xl py-2.5 px-2 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-md transition-all">16:9</button>
                        <button type="button" data-ratio="9:16" data-label="Story" class="aspect-btn rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">9:16</button>
                        <button type="button" data-ratio="1:1" data-label="Square" class="aspect-btn rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">1:1</button>
                        <button type="button" data-ratio="4:5" data-label="Portrait" class="aspect-btn rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">4:5</button>
                        <button type="button" data-ratio="3:2" data-label="Photo" class="aspect-btn rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">3:2</button>
                    </div>
                </section>

                <div id="formMessage" class="hidden rounded-2xl px-4 py-3 text-label-md font-semibold"></div>

                <!-- CTA -->
                <button
                    id="renderBtn"
                    class="relative w-full overflow-hidden py-4 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl font-headline-md text-headline-md flex items-center justify-center gap-3 hover:brightness-105 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                    type="button"
                >
                    <span class="absolute inset-0 carousel-shine opacity-40"></span>
                    <span id="renderBtnIcon" class="material-symbols-outlined relative" style="font-variation-settings:'FILL' 1;">play_arrow</span>
                    <span id="renderBtnText" class="relative">Mulai Render Otomatis</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- RIGHT PANEL -->
    <main class="xl:col-span-7 2xl:col-span-8 min-w-0">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-label-sm font-bold mb-3">
                    <span class="material-symbols-outlined text-[16px]">movie_creation</span>
                    Render Workspace
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span id="summaryReel" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">Reel -</span>
                <span id="summaryRatio" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">Rasio 16:9</span>
            </div>
        </div>

        <section class="rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/80 shadow-[0_18px_60px_rgba(0,0,0,0.06)] p-4 sm:p-6 min-h-[560px] backdrop-blur">
            <div id="renderGrid" class="grid min-h-[510px] grid-cols-1 gap-5 md:grid-cols-2">
                <div id="emptyRenderState" class="md:col-span-2 min-h-[510px] rounded-3xl border-2 border-dashed border-outline-variant/80 bg-gradient-to-br from-surface-container-lowest via-surface-container-low to-primary-container/10 flex flex-col items-center justify-center text-center p-8 overflow-hidden relative">
                    <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-primary-container/20 blur-3xl"></div>
                    <div class="absolute -bottom-20 -right-20 w-56 h-56 rounded-full bg-primary/10 blur-3xl"></div>

                    <div class="relative w-20 h-20 rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-lg flex items-center justify-center mb-5">
                        <span class="material-symbols-outlined text-5xl text-primary">movie_creation</span>
                    </div>

                    <h3 class="relative font-headline-md text-on-surface mb-2">Belum ada hasil render</h3>
                    <p class="relative text-body-md text-on-surface-variant max-w-md leading-relaxed">
                        Upload PDF storyboard, pilih reel, tambahkan referensi bila perlu, lalu klik Mulai Render Otomatis.
                    </p>

                    <div class="relative mt-6 flex flex-wrap justify-center gap-2">
                        <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">PDF storyboard</span>
                        <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Dropdown reel</span>
                        <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Tanpa contoh palsu</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Toast -->
<div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-[90] hidden max-w-sm rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-[0px_18px_55px_rgba(0,0,0,0.18)]">
    <div class="flex gap-3">
        <div id="toastIconWrap" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <span id="toastIcon" class="material-symbols-outlined">info</span>
        </div>
        <div>
            <p id="toastTitle" class="font-label-md text-label-md font-bold text-on-surface">Info</p>
            <p id="toastMessage" class="mt-1 text-label-sm text-on-surface-variant">Pesan</p>
        </div>
    </div>
</div>

<style>

    .reel-select-clean {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: none !important;
    }

    .reel-select-clean::-ms-expand {
        display: none;
    }

    .upload-zone.is-dragging {
        border-color: #20c4d8;
        background: rgba(32, 196, 216, 0.08);
        transform: translateY(-1px);
    }

    .upload-zone.is-ready {
        border-style: solid;
        border-color: rgba(0, 104, 116, 0.45);
        background: #ffffff;
    }

    .prompt-chip {
        border-radius: 9999px;
        background: #ffffff;
        border: 1px solid #bbc9cc;
        padding: 0.4rem 0.75rem;
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

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .carousel-shine::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 45%;
        height: 320%;
        transform: rotate(25deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
        animation: carouselShine 3.8s ease-in-out infinite;
    }

    @keyframes carouselShine {
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
        animation: shimmer 1.25s infinite;
    }

    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX_REFERENCE_FILES = 10;
    const MAX_PDF_SIZE = 30 * 1024 * 1024;
    const MAX_REFERENCE_SIZE = 10 * 1024 * 1024;
    const PDF_JS_WORKER = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const RENDER_ENDPOINT = @json(url('/carousel/render'));

    const state = {
        selectedPdf: null,
        referenceFiles: [],
        aspectRatio: '16:9',
        aspectLabel: 'Landscape',
        detectedReelCount: 0,
        pdfPageCount: 0,
        isRendering: false,
    };

    const elements = {
        pdfInput: document.getElementById('pdfInput'),
        pdfDropZone: document.getElementById('pdfDropZone'),
        pdfUploadText: document.getElementById('pdfUploadText'),
        pdfFileName: document.getElementById('pdfFileName'),
        pdfInfo: document.getElementById('pdfInfo'),
        pdfBadge: document.getElementById('pdfBadge'),

        referenceInput: document.getElementById('referenceInput'),
        referenceDropZone: document.getElementById('referenceDropZone'),
        referenceEmptyState: document.getElementById('referenceEmptyState'),
        referenceLoadedState: document.getElementById('referenceLoadedState'),
        referenceLoadedText: document.getElementById('referenceLoadedText'),
        referenceFileList: document.getElementById('referenceFileList'),
        clearReferenceBtn: document.getElementById('clearReferenceBtn'),

        reelSelect: document.getElementById('reelSelect'),
        reelCountBadge: document.getElementById('reelCountBadge'),

        extraPrompt: document.getElementById('extraPrompt'),
        promptCounter: document.getElementById('promptCounter'),
        aspectButtons: document.querySelectorAll('.aspect-btn'),
        ratioHint: document.getElementById('ratioHint'),

        statusPdf: document.getElementById('statusPdf'),
        statusReference: document.getElementById('statusReference'),
        statusReel: document.getElementById('statusReel'),
        summaryReel: document.getElementById('summaryReel'),
        summaryRatio: document.getElementById('summaryRatio'),
        workspaceSubtitle: document.getElementById('workspaceSubtitle'),

        formMessage: document.getElementById('formMessage'),
        renderBtn: document.getElementById('renderBtn'),
        renderBtnIcon: document.getElementById('renderBtnIcon'),
        renderBtnText: document.getElementById('renderBtnText'),
        renderGrid: document.getElementById('renderGrid'),

        toast: document.getElementById('toast'),
        toastIconWrap: document.getElementById('toastIconWrap'),
        toastIcon: document.getElementById('toastIcon'),
        toastTitle: document.getElementById('toastTitle'),
        toastMessage: document.getElementById('toastMessage'),
    };

    let toastTimer = null;

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = PDF_JS_WORKER;
    }

    function showToast(title, message, type = 'info') {
        clearTimeout(toastTimer);

        elements.toastTitle.textContent = title;
        elements.toastMessage.textContent = message;
        elements.toastIconWrap.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl';

        if (type === 'success') {
            elements.toastIcon.textContent = 'check_circle';
            elements.toastIconWrap.classList.add('bg-primary/10', 'text-primary');
        } else if (type === 'error') {
            elements.toastIcon.textContent = 'error';
            elements.toastIconWrap.classList.add('bg-error-container', 'text-on-error-container');
        } else {
            elements.toastIcon.textContent = 'info';
            elements.toastIconWrap.classList.add('bg-secondary-container', 'text-on-secondary-container');
        }

        elements.toast.classList.remove('hidden');
        toastTimer = setTimeout(() => elements.toast.classList.add('hidden'), 4200);
    }

    function showFormMessage(message, type = 'error') {
        elements.formMessage.className = 'rounded-2xl px-4 py-3 text-label-md font-semibold';

        if (type === 'success') {
            elements.formMessage.classList.add('bg-primary/10', 'text-primary');
        } else if (type === 'info') {
            elements.formMessage.classList.add('bg-secondary-container', 'text-on-secondary-container');
        } else {
            elements.formMessage.classList.add('bg-error-container', 'text-on-error-container');
        }

        elements.formMessage.textContent = message;
    }

    function clearFormMessage() {
        elements.formMessage.className = 'hidden rounded-2xl px-4 py-3 text-label-md font-semibold';
        elements.formMessage.textContent = '';
    }

    function setDragState(element, isActive) {
        element.classList.toggle('is-dragging', isActive);
    }

    function setupDropzone(element, onDrop) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
            element.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            element.addEventListener(eventName, () => setDragState(element, true));
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            element.addEventListener(eventName, () => setDragState(element, false));
        });

        element.addEventListener('drop', onDrop);
    }

    elements.pdfDropZone.addEventListener('click', () => elements.pdfInput.click());
    elements.referenceDropZone.addEventListener('click', (event) => {
        if (!event.target.closest('[data-reference-remove]')) {
            elements.referenceInput.click();
        }
    });

    setupDropzone(elements.pdfDropZone, async (event) => {
        const file = event.dataTransfer.files?.[0];
        if (file) await handlePdfFile(file);
    });

    setupDropzone(elements.referenceDropZone, (event) => {
        addReferenceFiles(Array.from(event.dataTransfer.files || []));
    });

    elements.pdfInput.addEventListener('change', async () => {
        const file = elements.pdfInput.files?.[0];

        if (file) {
            await handlePdfFile(file);
        }

        elements.pdfInput.value = '';
    });

    elements.referenceInput.addEventListener('change', () => {
        addReferenceFiles(Array.from(elements.referenceInput.files || []));
        elements.referenceInput.value = '';
    });

    elements.clearReferenceBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        state.referenceFiles = [];
        renderReferenceState();
        updateStatusSummary();
        showToast('Referensi dihapus', 'Semua foto referensi berhasil dihapus.', 'success');
    });

    elements.reelSelect.addEventListener('change', updateStatusSummary);

    elements.extraPrompt.addEventListener('input', () => {
        elements.promptCounter.textContent = `${elements.extraPrompt.value.length}/700`;
    });

    document.querySelectorAll('.prompt-chip').forEach((chip) => {
        chip.addEventListener('click', () => {
            const value = chip.dataset.promptChip || '';
            const current = elements.extraPrompt.value.trim();
            elements.extraPrompt.value = current ? `${current}\n${value}` : value;
            elements.promptCounter.textContent = `${elements.extraPrompt.value.length}/700`;
            elements.extraPrompt.focus();
        });
    });

    elements.aspectButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.aspectRatio = button.dataset.ratio;
            state.aspectLabel = button.dataset.label || button.dataset.ratio;
            setActiveSegment(elements.aspectButtons, button);
            updateStatusSummary();
        });
    });

    function setActiveSegment(buttons, selectedButton) {
        buttons.forEach((button) => {
            button.classList.remove('bg-primary-container', 'text-on-primary-container', 'shadow-sm');
            button.classList.add('text-on-surface-variant', 'hover:bg-surface-container-high');
        });

        selectedButton.classList.add('bg-primary-container', 'text-on-primary-container', 'shadow-sm');
        selectedButton.classList.remove('text-on-surface-variant', 'hover:bg-surface-container-high');
    }

    async function handlePdfFile(file) {
        clearFormMessage();

        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

        if (!isPdf) {
            showFormMessage('File storyboard harus berformat PDF.');
            showToast('Format tidak sesuai', 'Upload file dengan format PDF.', 'error');
            return;
        }

        if (file.size > MAX_PDF_SIZE) {
            showFormMessage('Ukuran PDF maksimal 30 MB.');
            showToast('PDF terlalu besar', 'Gunakan PDF berukuran maksimal 30 MB.', 'error');
            return;
        }

        state.selectedPdf = file;
        state.detectedReelCount = 0;
        state.pdfPageCount = 0;

        elements.pdfUploadText.textContent = 'PDF sedang dianalisis...';
        elements.pdfFileName.textContent = file.name;
        elements.pdfFileName.classList.remove('hidden');
        elements.pdfInfo.textContent = 'Membaca penomoran ide/reel dari nama file dan isi PDF...';
        elements.pdfBadge.classList.add('hidden');
        elements.pdfDropZone.classList.add('is-ready');

        resetReelDropdown('Mendeteksi reel...');

        const filenameHintCount = detectCountFromFilename(file.name);

        if (filenameHintCount > 0) {
            populateReelDropdown(filenameHintCount);
            elements.pdfInfo.textContent = `Nama file menunjukkan ${filenameHintCount} reel. Isi PDF tetap sedang dianalisis...`;
        }

        try {
            const detection = await detectPdfReelCount(file);
            state.detectedReelCount = Math.max(1, detection.reelCount || 1);
            state.pdfPageCount = detection.pageCount || 0;

            populateReelDropdown(state.detectedReelCount);

            const sourceText = {
                filename: 'nama file',
                pdf_text: 'isi teks PDF',
                pdf_pages: 'jumlah halaman PDF',
                mixed: 'gabungan deteksi'
            }[detection.detectedFrom] || 'deteksi otomatis';

            const usesPageFallback = detection.detectedFrom === 'pdf_pages';

            elements.pdfUploadText.textContent = 'PDF berhasil dimuat';
            elements.pdfInfo.textContent = `${state.detectedReelCount} reel/carousel terdeteksi dari ${sourceText}. Halaman PDF: ${state.pdfPageCount || '-'} halaman.`;
            elements.pdfBadge.classList.remove('hidden');

            showFormMessage(
                usesPageFallback
                    ? `PDF siap. Dropdown reel mengikuti ${state.detectedReelCount} halaman PDF.`
                    : `PDF siap. Sistem menemukan ${state.detectedReelCount} reel/carousel.`,
                usesPageFallback ? 'info' : 'success'
            );
            showToast(
                'PDF berhasil dimuat',
                usesPageFallback ? `${state.detectedReelCount} pilihan reel dibuat dari halaman PDF.` : `${state.detectedReelCount} reel/carousel terdeteksi.`,
                usesPageFallback ? 'info' : 'success'
            );
        } catch (error) {
            console.error(error);

            state.detectedReelCount = 1;
            state.pdfPageCount = 1;
            populateReelDropdown(1);

            elements.pdfUploadText.textContent = 'PDF berhasil dimuat';
            elements.pdfInfo.textContent = 'PDF terbaca, tetapi jumlah reel belum bisa dianalisis akurat. Dropdown disiapkan 1 reel sementara.';
            elements.pdfBadge.classList.remove('hidden');

            showFormMessage('PDF berhasil diupload, tetapi jumlah reel belum bisa dianalisis akurat.', 'success');
            showToast('PDF dimuat', 'Dropdown disiapkan 1 reel sementara.', 'info');
        }

        updateStatusSummary();
    }

    async function detectPdfReelCount(file) {
        const buffer = await file.arrayBuffer();
        let pageCount = 0;
        let pdfText = '';

        if (window.pdfjsLib) {
            try {
                const pdf = await pdfjsLib.getDocument({ data: new Uint8Array(buffer) }).promise;
                pageCount = pdf.numPages || 0;
                pdfText = await extractPdfText(pdf);
            } catch (error) {
                console.warn('PDF.js gagal membaca file, memakai estimasi halaman mentah.', error);
            }
        }

        if (!pageCount && !pdfText) {
            const raw = extractRawText(buffer);
            pageCount = estimatePdfPagesFromRaw(raw);
            pdfText = raw;
        }

        const filenameCount = detectCountFromFilename(file.name) || detectCountFromText(file.name, 'filename');
        const textCount = detectCountFromText(pdfText, 'pdf_text');

        if (filenameCount > 1 && filenameCount <= 100 && filenameCount >= textCount) {
            return {
                reelCount: filenameCount,
                pageCount,
                detectedFrom: 'filename',
                filenameCount,
                textCount,
            };
        }

        // Nama file dari generator sering memuat total reel di awal, misalnya
        // "20_Ide_Konten_Reels" atau "60_IDE_KONTEN_VIDEO_REELS".
        if (textCount > 1 && textCount <= 100) {
            return {
                reelCount: textCount,
                pageCount,
                detectedFrom: 'pdf_text',
                filenameCount,
                textCount,
            };
        }

        if (filenameCount > 0 && filenameCount <= 100) {
            return {
                reelCount: filenameCount,
                pageCount,
                detectedFrom: 'filename',
                filenameCount,
                textCount,
            };
        }

        const pageBasedCount = Math.min(100, Math.max(1, pageCount || 1));

        return {
            reelCount: pageBasedCount,
            pageCount,
            detectedFrom: 'pdf_pages',
            filenameCount,
            textCount,
        };
    }

    async function extractPdfText(pdf) {
        const pagesToScan = Math.min(pdf.numPages || 0, 100);
        const chunks = [];

        for (let pageNumber = 1; pageNumber <= pagesToScan; pageNumber++) {
            try {
                const page = await pdf.getPage(pageNumber);
                const content = await page.getTextContent();

                const pageText = content.items
                    .map((item) => item.str || '')
                    .join(' ');

                chunks.push(pageText);
            } catch (error) {
                console.warn(`Gagal membaca teks halaman ${pageNumber}`, error);
            }
        }

        return chunks.join('\n');
    }

    function detectCountFromFilename(filename) {
        if (!filename) return 0;

        const nameOnly = String(filename)
            .replace(/\.[^.]+$/, '')
            .replace(/[_\-]+/g, ' ')
            .replace(/[()[\]{}]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .toLowerCase();

        const filenamePatterns = [
            /\b(\d{1,3})\s+(?:ide\s+)?(?:konten\s+)?(?:video\s+)?(?:reels?|carousel|corousel|karosel)\b/,
            /\b(\d{1,3})\s+(?:ide|konten)\b/,
            /\b(?:total|jumlah|berisi|contains?)\s+(\d{1,3})\s+(?:ide|konten|video|reels?|carousel|corousel|karosel)\b/,
        ];

        for (const pattern of filenamePatterns) {
            const match = nameOnly.match(pattern);
            const value = Number.parseInt(match?.[1], 10);

            if (value >= 1 && value <= 100) {
                return value;
            }
        }

        return 0;
    }

    function detectCountFromText(text, source = 'pdf_text') {
        if (!text) return 0;

        const normalized = String(text)
            .replace(/[_\-]+/g, ' ')
            .replace(/[^\S\r\n]+/g, ' ')
            .toLowerCase();

        const numbers = [];

        const patterns = [
            /\b(\d{1,3})\s*(?:ide\s*)?(?:konten\s*)?(?:reels?|reel|carousel|corousel|karosel)\b/g,
            /\b(?:ide\s*konten|konten|reels?|reel|carousel|corousel|karosel)\s*(?:ke)?\s*[-:#.]?\s*(\d{1,3})\b/g,
            /\b(?:total|jumlah|terdapat|ada|berisi|contains?)\s*(\d{1,3})\s*(?:ide|konten|reels?|reel|carousel|corousel|karosel)\b/g,
            /\b(?:ide|konten)\s*(\d{1,3})\b/g,
        ];

        patterns.forEach((pattern) => {
            for (const match of normalized.matchAll(pattern)) {
                const value = Number.parseInt(match[1], 10);

                if (value >= 1 && value <= 100) {
                    numbers.push(value);
                }
            }
        });

        // Jangan gunakan daftar angka umum karena nomor halaman/subbagian dapat
        // keliru dibaca sebagai jumlah reel (misalnya halaman 80).
        if (false && source !== 'filename') {
            const listNumbers = [];
            const listPattern = /(?:^|[\n\r\s])(\d{1,3})\s*[.)]\s+(?=[a-zA-ZÀ-ÿ])/g;

            for (const match of normalized.matchAll(listPattern)) {
                const value = Number.parseInt(match[1], 10);

                if (value >= 1 && value <= 100) {
                    listNumbers.push(value);
                }
            }

            if (listNumbers.length >= 3) {
                numbers.push(Math.max(...listNumbers));
            }
        }

        if (!numbers.length) return 0;

        const uniqueNumbers = [...new Set(numbers)].sort((a, b) => a - b);
        const numberSet = new Set(uniqueNumbers);
        let contiguousLast = 0;

        while (numberSet.has(contiguousLast + 1)) {
            contiguousLast += 1;
        }

        // Jika ada urutan ide yang jelas (misalnya 1–60), abaikan angka loncat
        // seperti 80 yang biasanya berasal dari halaman, versi, atau metadata.
        if (contiguousLast >= 3) return contiguousLast;

        return Math.max(...uniqueNumbers);
    }

    function extractRawText(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        const chunkSize = 8192;

        for (let i = 0; i < bytes.length; i += chunkSize) {
            binary += String.fromCharCode.apply(null, bytes.slice(i, i + chunkSize));
        }

        return binary;
    }

    function estimatePdfPagesFromRaw(rawText) {
        const matches = rawText.match(/\/Type\s*\/Page\b/g);
        return matches?.length || 1;
    }

    function resetReelDropdown(label) {
        elements.reelSelect.innerHTML = `<option value="">${escapeHtml(label)}</option>`;
        elements.reelSelect.disabled = true;
        elements.reelCountBadge.textContent = label;
    }

    function populateReelDropdown(count) {
        const safeCount = Math.min(100, Math.max(1, Number.parseInt(count, 10) || 1));
        const previousSelection = Number.parseInt(elements.reelSelect.value, 10) || 1;
        elements.reelSelect.innerHTML = '';

        for (let i = 1; i <= safeCount; i += 1) {
            const option = document.createElement('option');
            option.value = String(i);
            option.textContent = `Reel ${i}`;
            elements.reelSelect.appendChild(option);
        }

        elements.reelSelect.disabled = false;
        elements.reelSelect.value = String(Math.min(previousSelection, safeCount));
        elements.reelCountBadge.textContent = `${safeCount} reel tersedia`;
        state.detectedReelCount = safeCount;
        updateStatusSummary();
    }

    function addReferenceFiles(files) {
        clearFormMessage();

        const imageFiles = files.filter((file) => ['image/jpeg', 'image/png', 'image/webp'].includes(file.type) && file.size <= MAX_REFERENCE_SIZE);

        if (!imageFiles.length) {
            showFormMessage('Foto referensi harus berformat gambar.');
            showToast('Format tidak sesuai', 'Upload file gambar untuk referensi karakter.', 'error');
            return;
        }

        const existingKeys = new Set(state.referenceFiles.map(getFileKey));
        const uniqueFiles = imageFiles.filter((file) => !existingKeys.has(getFileKey(file)));
        const availableSlots = MAX_REFERENCE_FILES - state.referenceFiles.length;
        const filesToAdd = uniqueFiles.slice(0, availableSlots);

        state.referenceFiles = [...state.referenceFiles, ...filesToAdd];

        renderReferenceState();
        updateStatusSummary();

        if (uniqueFiles.length > availableSlots) {
            showToast('Sebagian foto tidak ditambahkan', `Maksimal ${MAX_REFERENCE_FILES} foto referensi.`, 'info');
        } else {
            showToast('Referensi berhasil dimuat', `${filesToAdd.length} foto ditambahkan.`, 'success');
        }
    }

    function getFileKey(file) {
        return `${file.name}-${file.size}-${file.lastModified}`;
    }

    function renderReferenceState() {
        const total = state.referenceFiles.length;

        if (!total) {
            elements.referenceEmptyState.classList.remove('hidden');
            elements.referenceLoadedState.classList.add('hidden');
            elements.clearReferenceBtn.classList.add('hidden');
            return;
        }

        elements.referenceEmptyState.classList.add('hidden');
        elements.referenceLoadedState.classList.remove('hidden');
        elements.clearReferenceBtn.classList.remove('hidden');

        elements.referenceLoadedText.textContent = total === 1
            ? '1 foto referensi siap dipakai'
            : `${total} foto referensi siap dipakai`;

        elements.referenceFileList.innerHTML = '';

        state.referenceFiles.slice(0, 5).forEach((file, index) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex max-w-[150px] items-center gap-1 rounded-full bg-white px-3 py-1 text-[11px] font-bold text-on-surface-variant shadow-sm';
            chip.innerHTML = `
                <span class="material-symbols-outlined text-[14px] text-[#00875A]">image</span>
                <span class="truncate">${escapeHtml(file.name || `Foto ${index + 1}`)}</span>
            `;
            elements.referenceFileList.appendChild(chip);
        });

        if (total > 5) {
            const more = document.createElement('span');
            more.className = 'inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-bold text-on-surface-variant shadow-sm';
            more.textContent = `+${total - 5} foto`;
            elements.referenceFileList.appendChild(more);
        }
    }

    function updateStatusSummary() {
        elements.statusPdf.textContent = state.selectedPdf ? 'PDF Siap' : 'PDF';
        elements.statusPdf.nextElementSibling.textContent = state.selectedPdf ? 'Terupload' : 'Belum';

        elements.statusReference.textContent = `${state.referenceFiles.length} Foto`;
        elements.statusReference.nextElementSibling.textContent = 'Referensi';

        elements.statusReel.textContent = state.detectedReelCount ? `${state.detectedReelCount} Reel` : '- Reel';
        elements.statusReel.nextElementSibling.textContent = 'Terdeteksi';

        elements.summaryReel.textContent = elements.reelSelect.value ? `Reel ${elements.reelSelect.value}` : 'Reel -';
        elements.summaryRatio.textContent = `Rasio ${state.aspectRatio}`;
        elements.ratioHint.textContent = state.aspectLabel;

        elements.workspaceSubtitle.textContent = state.selectedPdf
            ? `${state.selectedPdf.name} • ${state.detectedReelCount || 0} reel • ${state.aspectRatio}`
            : 'Hasil baru muncul setelah proses render berhasil.';
    }

    elements.renderBtn.addEventListener('click', async () => {
        clearFormMessage();

        if (!state.selectedPdf) {
            showFormMessage('Upload PDF storyboard dulu sebelum mulai render.');
            showToast('PDF belum ada', 'Upload PDF storyboard terlebih dahulu.', 'error');
            return;
        }

        if (!elements.reelSelect.value) {
            showFormMessage('Pilih nomor reel terlebih dahulu.');
            showToast('Reel belum dipilih', 'Pilih nomor reel yang ingin dirender.', 'error');
            return;
        }

        await requestRender();
    });

    async function requestRender() {
        setRenderButtonLoading(true);
        showLoadingState();

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('storyboard_pdf', state.selectedPdf);
        formData.append('reel_number', elements.reelSelect.value);
        formData.append('aspect_ratio', state.aspectRatio);
        formData.append('extra_prompt', elements.extraPrompt.value.trim());

        state.referenceFiles.forEach((file) => {
            formData.append('reference_photos[]', file);
        });

        try {
            const response = await fetch(RENDER_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const validationMessage = data.errors
                    ? Object.values(data.errors).flat().find(Boolean)
                    : null;
                throw new Error(validationMessage || data.message || `Render gagal dengan status ${response.status}.`);
            }
            const results = normalizeRenderResults(data).slice(0, 12);

            if (!results.length) {
                showEmptyResult('Render selesai, tetapi backend belum mengirim URL hasil gambar/video.');
                showFormMessage('Render selesai, tetapi tidak ada URL hasil yang diterima dari backend.', 'success');
                showToast('Belum ada output', 'Backend belum mengirim URL hasil render.', 'info');
                return;
            }

            renderResults(results);
            showFormMessage(`${results.length} hasil render berhasil ditampilkan.`, 'success');
            showToast('Render berhasil', `${results.length} hasil render ditampilkan.`, 'success');
        } catch (error) {
            console.error(error);
            showEmptyResult(error.message || 'Render belum bisa ditampilkan. Silakan periksa input lalu coba kembali.');
            showFormMessage(error.message || 'Terjadi kesalahan saat proses render.');
            showToast('Render gagal', error.message || 'Silakan coba kembali.', 'error');
        } finally {
            setRenderButtonLoading(false);
        }
    }

    function setRenderButtonLoading(isLoading) {
        state.isRendering = isLoading;
        elements.renderBtn.disabled = isLoading;
        elements.pdfDropZone.disabled = isLoading;
        elements.referenceDropZone.disabled = isLoading;
        elements.reelSelect.disabled = isLoading;
        elements.extraPrompt.disabled = isLoading;
        elements.aspectButtons.forEach((button) => { button.disabled = isLoading; });
        elements.renderBtnIcon.textContent = isLoading ? 'refresh' : 'play_arrow';
        elements.renderBtnIcon.classList.toggle('animate-spin', isLoading);
        elements.renderBtnText.textContent = isLoading ? `Memproses Reel ${elements.reelSelect.value}...` : 'Mulai Render Otomatis';
    }

    function getAspectRatioValue() {
        const [width, height] = state.aspectRatio.split(':').map(Number);
        return `${width} / ${height}`;
    }

    function showLoadingState() {
        elements.renderGrid.innerHTML = '';

        for (let i = 0; i < 4; i += 1) {
            const skeleton = document.createElement('div');
            skeleton.className = 'rounded-3xl border border-outline-variant bg-surface-container-lowest overflow-hidden animate-pulse shadow-sm';
            skeleton.innerHTML = `
                <div class="skeleton-shimmer" style="aspect-ratio: ${getAspectRatioValue()};"></div>
                <div class="p-4 space-y-3">
                    <div class="skeleton-shimmer h-4 w-2/5 rounded-full"></div>
                    <div class="skeleton-shimmer h-3 w-full rounded-full"></div>
                    <div class="skeleton-shimmer h-3 w-3/4 rounded-full"></div>
                </div>
            `;
            elements.renderGrid.appendChild(skeleton);
        }
    }

    function showEmptyResult(message) {
        elements.renderGrid.innerHTML = `
            <div class="md:col-span-2 min-h-[510px] rounded-3xl border-2 border-dashed border-outline-variant/80 bg-gradient-to-br from-surface-container-lowest via-surface-container-low to-primary-container/10 flex flex-col items-center justify-center text-center p-8">
                <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-surface-container-lowest text-primary shadow-sm border border-outline-variant">
                    <span class="material-symbols-outlined text-[44px]">movie_creation</span>
                </div>
                <h3 class="font-headline-md text-on-surface">Belum ada hasil render</h3>
                <p class="mt-2 max-w-md text-body-md text-on-surface-variant">${escapeHtml(message)}</p>
            </div>
        `;
    }

    function normalizeRenderResults(data) {
        if (!data) return [];

        const possibleArrays = [
            data.results,
            data.images,
            data.scenes,
            data.outputs,
            data.data?.results,
            data.data?.images,
            data.data?.scenes,
            Array.isArray(data.data) ? data.data : null,
            Array.isArray(data) ? data : null,
        ].filter(Boolean);

        let rawResults = possibleArrays.find(Array.isArray) || [];

        if (!rawResults.length) {
            const singleUrl = data.url || data.image_url || data.video_url || data.output_url || data.result;
            rawResults = singleUrl ? [singleUrl] : [];
        }

        return rawResults
            .map((item, index) => {
                if (typeof item === 'string') {
                    return {
                        title: `Scene ${index + 1}`,
                        url: item,
                        status: 'Completed',
                        prompt: '',
                    };
                }

                return {
                    title: item?.title || item?.name || `Scene ${item?.scene_number || index + 1}`,
                    url: item?.image_url || item?.video_url || item?.output_url || item?.url || item?.path || '',
                    status: item?.status || 'Completed',
                    prompt: item?.prompt || item?.description || item?.caption || '',
                };
            })
            .filter((item) => item.url);
    }

    function renderResults(results) {
        elements.renderGrid.innerHTML = '';
        elements.workspaceSubtitle.textContent = `${results.length} hasil render berhasil dibuat.`;

        results.forEach((result, index) => {
            elements.renderGrid.appendChild(createResultCard(result, index));
        });
    }

    function createResultCard(result, index) {
        const isVideo = /\.(mp4|webm|mov|m4v)(\?|$)/i.test(result.url);
        const card = document.createElement('article');
        card.className = 'group relative overflow-hidden rounded-3xl border border-outline-variant bg-surface-container-lowest shadow-[0_14px_40px_rgba(0,0,0,0.08)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_22px_60px_rgba(0,0,0,0.12)]';

        const mediaWrap = document.createElement('div');
        mediaWrap.className = 'relative overflow-hidden bg-surface-container';
        mediaWrap.style.aspectRatio = getAspectRatioValue();

        if (isVideo) {
            const video = document.createElement('video');
            video.className = 'h-full w-full object-cover';
            video.src = result.url;
            video.controls = true;
            video.playsInline = true;
            mediaWrap.appendChild(video);
        } else {
            const image = document.createElement('img');
            image.className = 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105';
            image.alt = result.title || `Scene ${index + 1}`;
            image.src = result.url;
            image.loading = 'lazy';
            mediaWrap.appendChild(image);
        }

        const topBadge = document.createElement('span');
        topBadge.className = 'absolute left-3 top-3 rounded-full bg-black/45 px-3 py-1 text-[11px] font-bold text-white backdrop-blur';
        topBadge.textContent = `Reel ${elements.reelSelect.value || '-'} • ${state.aspectRatio}`;

        const overlay = document.createElement('div');
        overlay.className = 'absolute inset-0 bg-gradient-to-t from-black/70 via-black/15 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between gap-3 p-4';

        const actions = document.createElement('div');
        actions.className = 'flex gap-2';
        actions.appendChild(createActionLink(result.url, 'download', 'Download hasil', true));
        actions.appendChild(createActionLink(result.url, 'zoom_in', 'Lihat hasil', false));

        const outputType = document.createElement('span');
        outputType.className = 'px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-[11px] font-bold';
        outputType.textContent = isVideo ? 'VIDEO' : 'IMAGE';

        overlay.appendChild(actions);
        overlay.appendChild(outputType);
        mediaWrap.appendChild(topBadge);
        mediaWrap.appendChild(overlay);

        const footer = document.createElement('div');
        footer.className = 'p-4 bg-white flex items-start justify-between gap-4';

        const titleWrap = document.createElement('div');
        titleWrap.className = 'min-w-0';

        const title = document.createElement('p');
        title.className = 'text-label-md font-extrabold text-on-surface truncate';
        title.textContent = result.title || `Scene ${index + 1}`;

        const prompt = document.createElement('p');
        prompt.className = 'mt-1 text-[11px] font-semibold text-on-surface-variant line-clamp-2';
        prompt.textContent = result.prompt || `Hasil render Reel ${elements.reelSelect.value} dengan rasio ${state.aspectRatio}.`;

        const status = document.createElement('span');
        status.className = 'shrink-0 rounded-full bg-primary/10 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-primary';
        status.textContent = result.status || 'Completed';

        titleWrap.appendChild(title);
        titleWrap.appendChild(prompt);
        footer.appendChild(titleWrap);
        footer.appendChild(status);

        card.appendChild(mediaWrap);
        card.appendChild(footer);

        return card;
    }

    function createActionLink(href, iconName, label, download = false) {
        const link = document.createElement('a');
        link.href = href;
        link.className = 'inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-xl ring-1 ring-white/25 hover:bg-white/35 hover:scale-105 active:scale-95 transition-all';
        link.setAttribute('aria-label', label);

        if (download) {
            link.download = '';
        } else {
            link.target = '_blank';
            link.rel = 'noopener';
        }

        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined';
        icon.textContent = iconName;
        link.appendChild(icon);

        return link;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    renderReferenceState();
    updateStatusSummary();
    elements.promptCounter.textContent = `${elements.extraPrompt.value.length}/700`;
});
</script>
@endsection
