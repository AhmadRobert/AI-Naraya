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
                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">auto_fix_high</span>
                            AI Photo Editor
                        </div>
                        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Edit Foto</h1>
                        <p class="mt-2 text-body-md text-on-surface-variant">Upload 1 foto, tulis instruksi edit, lalu buat hasil visual yang clean dan profesional.</p>
                    </div>

                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container items-center justify-center shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">photo_filter</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-7">
                <!-- 1. Upload Section -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">1</span>
                            Upload Foto
                        </h2>

                        <span id="photoStatusBadge" class="px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold">
                            Belum dipilih
                        </span>
                    </div>

                    <input id="editImageInput" class="hidden" type="file" accept="image/*">

                    <div id="uploadDropArea" class="upload-zone min-h-[260px] rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 p-4 transition-all cursor-pointer overflow-hidden relative">
                        <div id="uploadEmptyTile" class="absolute inset-4 rounded-2xl border border-outline-variant/50 bg-surface-container-lowest/70 flex flex-col items-center justify-center text-center p-5 hover:bg-white hover:border-primary transition-all">
                            <span class="w-16 h-16 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined text-4xl">add_photo_alternate</span>
                            </span>
                            <span class="font-bold text-on-surface">Klik atau drag foto ke sini</span>
                            <span class="text-label-sm text-on-surface-variant mt-1">PNG, JPG, WEBP · 1 foto utama</span>
                        </div>

                        <div id="previewFrame" class="hidden absolute inset-4 rounded-2xl overflow-hidden border border-outline-variant bg-surface-container-lowest shadow-sm">
                            <img id="previewImageBlur" class="absolute inset-0 w-full h-full object-cover blur-2xl scale-110 opacity-25" alt="">
                            <img id="previewImage" class="relative z-10 w-full h-full object-contain" alt="Preview foto yang dipilih">

                            <div class="absolute left-3 right-3 bottom-3 z-20 rounded-2xl bg-white/85 backdrop-blur-xl border border-white/70 p-3 shadow-[0_12px_35px_rgba(0,0,0,0.12)]">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p id="selectedFileName" class="truncate text-label-md font-extrabold text-on-surface">-</p>
                                        <p id="selectedFileMeta" class="mt-0.5 text-[11px] font-semibold text-on-surface-variant">Siap diedit</p>
                                    </div>

                                    <button id="removePhotoButton" type="button" class="shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-error-container text-error hover:brightness-95 transition-all" aria-label="Hapus foto" disabled>
                                        <span class="material-symbols-outlined text-[20px]">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-start gap-2">
                        <span id="uploadStatusIcon" class="material-symbols-outlined text-[18px] text-on-surface-variant mt-0.5">info</span>
                        <p id="uploadMessage" class="text-label-sm text-on-surface-variant">Belum ada foto dipilih. Upload foto terlebih dahulu.</p>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button id="choosePhotoButton" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary text-on-primary px-5 py-3.5 font-extrabold shadow-[0_14px_30px_rgba(0,104,116,0.18)] hover:brightness-110 active:scale-[0.98] transition-all">
                            <span class="material-symbols-outlined">add_photo_alternate</span>
                            Pilih Foto
                        </button>

                        <button id="replacePhotoButton" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant bg-surface-container-lowest px-5 py-3.5 font-extrabold text-on-surface hover:bg-surface-container transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <span class="material-symbols-outlined">sync</span>
                            Ganti Foto
                        </button>
                    </div>
                </section>

                <!-- 2. Instructions Section -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">2</span>
                            Prompt Edit
                        </h2>

                        <button id="enhancePromptButton" class="text-primary flex items-center gap-1 hover:underline text-label-sm font-bold" type="button">
                            <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                            Enhance Prompt
                        </button>
                    </div>

                    <div class="relative">
                        <textarea
                            id="promptInput"
                            maxlength="800"
                            class="w-full h-36 p-4 pb-12 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md resize-none outline-none placeholder:text-on-surface-variant/60"
                            placeholder="Contoh: ubah background menjadi pantai sunset, hapus objek di belakang, pertahankan wajah asli, lighting cinematic, warna warm, hasil natural."
                        ></textarea>

                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-3">
                            <span id="promptCounter" class="text-[11px] font-bold text-on-surface-variant">0/800</span>
                            <span class="px-2 py-1 rounded-full bg-surface-container-lowest text-[11px] text-on-surface-variant border border-outline-variant/60">Opsional</span>
                        </div>
                    </div>
                </section>

                <!-- 3. Ratio Selection -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">3</span>
                            Pilih Rasio
                        </h2>
                        <span id="ratioHint" class="text-label-sm text-on-surface-variant font-medium">Square</span>
                    </div>

                    <div id="ratioGroup" class="grid grid-cols-5 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-ratio="1:1" data-label="Square" class="ratio-button rounded-xl py-2.5 px-2 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-md transition-all">1:1</button>
                        <button type="button" data-ratio="16:9" data-label="Landscape" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">16:9</button>
                        <button type="button" data-ratio="9:16" data-label="Story" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">9:16</button>
                        <button type="button" data-ratio="4:5" data-label="Portrait" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">4:5</button>
                        <button type="button" data-ratio="3:2" data-label="Photo" class="ratio-button rounded-xl py-2.5 px-2 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">3:2</button>
                    </div>
                </section>

                <!-- 4. Result Count -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">4</span>
                            Jumlah Hasil
                        </h2>
                        <span class="text-label-sm text-on-surface-variant font-medium">Estimasi: <span id="creditEstimate" class="text-primary font-bold">4 Kredit</span></span>
                    </div>

                    <div id="countGroup" class="grid grid-cols-4 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-count="1" class="count-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">1</button>
                        <button type="button" data-count="2" class="count-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">2</button>
                        <button type="button" data-count="4" class="count-button rounded-xl py-2.5 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-md transition-all">4</button>
                        <button type="button" data-count="8" class="count-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">8</button>
                    </div>
                </section>

                <!-- CTA -->
                <button id="editButton" class="relative w-full overflow-hidden py-4 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl font-headline-md text-headline-md flex items-center justify-center gap-3 hover:brightness-105 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none" type="button">
                    <span class="absolute inset-0 edit-shine opacity-40"></span>
                    <span id="editButtonIcon" class="material-symbols-outlined relative" style="font-variation-settings:'FILL' 1;">auto_fix_high</span>
                    <span id="editButtonText" class="relative">Edit Foto Sekarang</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- RIGHT PANEL -->
    <main class="xl:col-span-7 2xl:col-span-8 min-w-0">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-label-sm font-bold mb-3">
                    <span class="material-symbols-outlined text-[16px]">image</span>
                    Preview Output
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span id="outputRatioBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">Rasio 1:1</span>
                <span id="outputCountBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">4 Hasil</span>
            </div>
        </div>

        <section class="rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/80 shadow-[0_18px_60px_rgba(0,0,0,0.06)] p-4 sm:p-6 min-h-[560px] backdrop-blur">
            <div id="resultsEmptyState" class="min-h-[510px] rounded-3xl border-2 border-dashed border-outline-variant/80 bg-gradient-to-br from-surface-container-lowest via-surface-container-low to-primary-container/10 flex flex-col items-center justify-center text-center p-8 overflow-hidden relative">
                <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-primary-container/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-56 h-56 rounded-full bg-primary/10 blur-3xl"></div>

                <div class="relative w-20 h-20 rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-lg flex items-center justify-center mb-5">
                    <span id="emptyStateIcon" class="material-symbols-outlined text-5xl text-primary">photo_filter</span>
                </div>

                <h3 id="emptyStateTitle" class="relative font-headline-md text-on-surface mb-2">Belum ada hasil edit</h3>
                <p id="emptyStateText" class="relative text-body-md text-on-surface-variant max-w-md leading-relaxed">Upload foto, isi prompt edit, pilih rasio dan jumlah hasil, lalu klik tombol Edit Foto Sekarang.</p>

                <div class="relative mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">1 foto utama</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Prompt opsional</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Tanpa contoh palsu</span>
                </div>
            </div>

            <div id="resultsGrid" class="hidden grid grid-cols-1 md:grid-cols-2 gap-5"></div>
        </section>
    </main>
</div>

<style>
    .upload-zone.is-dragging {
        border-color: #20c4d8;
        background: rgba(32, 196, 216, 0.08);
        transform: translateY(-1px);
    }

    .edit-shine::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 45%;
        height: 320%;
        transform: rotate(25deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
        animation: editShine 3.8s ease-in-out infinite;
    }

    @keyframes editShine {
        0%, 45% { left: -50%; }
        100% { left: 130%; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const EDIT_ENDPOINT = @json(url('/edit/generate'));

    const editImageInput = document.getElementById('editImageInput');
    const uploadDropArea = document.getElementById('uploadDropArea');
    const uploadEmptyTile = document.getElementById('uploadEmptyTile');
    const previewFrame = document.getElementById('previewFrame');
    const previewImage = document.getElementById('previewImage');
    const previewImageBlur = document.getElementById('previewImageBlur');
    const selectedFileName = document.getElementById('selectedFileName');
    const selectedFileMeta = document.getElementById('selectedFileMeta');
    const uploadMessage = document.getElementById('uploadMessage');
    const uploadStatusIcon = document.getElementById('uploadStatusIcon');
    const photoStatusBadge = document.getElementById('photoStatusBadge');
    const choosePhotoButton = document.getElementById('choosePhotoButton');
    const replacePhotoButton = document.getElementById('replacePhotoButton');
    const removePhotoButton = document.getElementById('removePhotoButton');
    const promptInput = document.getElementById('promptInput');
    const promptCounter = document.getElementById('promptCounter');
    const promptChips = document.querySelectorAll('.prompt-chip');
    const enhancePromptButton = document.getElementById('enhancePromptButton');
    const ratioButtons = document.querySelectorAll('.ratio-button');
    const countButtons = document.querySelectorAll('.count-button');
    const ratioHint = document.getElementById('ratioHint');
    const creditEstimate = document.getElementById('creditEstimate');
    const outputRatioBadge = document.getElementById('outputRatioBadge');
    const outputCountBadge = document.getElementById('outputCountBadge');
    const editButton = document.getElementById('editButton');
    const editButtonIcon = document.getElementById('editButtonIcon');
    const editButtonText = document.getElementById('editButtonText');
    const resultSubtitle = document.getElementById('resultSubtitle');
    const resultsEmptyState = document.getElementById('resultsEmptyState');
    const emptyStateIcon = document.getElementById('emptyStateIcon');
    const emptyStateTitle = document.getElementById('emptyStateTitle');
    const emptyStateText = document.getElementById('emptyStateText');
    const resultsGrid = document.getElementById('resultsGrid');

    const activeClasses = ['bg-primary-container', 'text-on-primary-container', 'shadow-sm'];
    const inactiveClasses = ['text-on-surface-variant', 'hover:bg-surface-container-high'];

    let selectedFile = null;
    let selectedPreviewUrl = null;
    let selectedRatio = '1:1';
    let selectedRatioLabel = 'Square';
    let selectedCount = 4;
    let isProcessing = false;

    function formatFileSize(bytes) {
        if (!bytes) return '0 KB';

        const units = ['B', 'KB', 'MB', 'GB'];
        const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        const size = bytes / Math.pow(1024, index);

        return `${size.toFixed(index === 0 ? 0 : 1)} ${units[index]}`;
    }

    function setButtonActive(buttons, activeButton) {
        buttons.forEach((button) => {
            button.classList.remove(...activeClasses, ...inactiveClasses);

            if (button === activeButton) {
                button.classList.add(...activeClasses);
            } else {
                button.classList.add(...inactiveClasses);
            }
        });
    }

    function getAspectRatioValue() {
        const [width, height] = selectedRatio.split(':').map(Number);
        return `${width} / ${height}`;
    }

    function updateOutputMeta() {
        ratioHint.textContent = selectedRatioLabel;
        creditEstimate.textContent = `${selectedCount} Kredit`;
        outputRatioBadge.textContent = `Rasio ${selectedRatio}`;
        outputCountBadge.textContent = `${selectedCount} Hasil`;
    }

    function updatePromptCounter() {
        promptCounter.textContent = `${promptInput.value.length}/800`;
    }

    function showUploadMessage(message, type = 'info') {
        uploadMessage.textContent = message;
        uploadMessage.className = 'text-label-sm';
        uploadStatusIcon.className = 'material-symbols-outlined text-[18px] mt-0.5';

        if (type === 'error') {
            uploadMessage.classList.add('text-red-600');
            uploadStatusIcon.classList.add('text-red-600');
            uploadStatusIcon.textContent = 'error';
            return;
        }

        if (type === 'success') {
            uploadMessage.classList.add('text-primary');
            uploadStatusIcon.classList.add('text-primary');
            uploadStatusIcon.textContent = 'check_circle';
            return;
        }

        uploadMessage.classList.add('text-on-surface-variant');
        uploadStatusIcon.classList.add('text-on-surface-variant');
        uploadStatusIcon.textContent = 'info';
    }

    function refreshFormState() {
        const isReady = Boolean(selectedFile) && !isProcessing;

        editButton.disabled = !isReady;
        replacePhotoButton.disabled = !selectedFile || isProcessing;
        removePhotoButton.disabled = !selectedFile || isProcessing;

        photoStatusBadge.textContent = selectedFile ? 'Foto siap' : 'Belum dipilih';
        photoStatusBadge.className = selectedFile
            ? 'px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-sm font-bold'
            : 'px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold';
    }

    function openFilePicker() {
        if (isProcessing) return;
        editImageInput.click();
    }

    function clearSelectedPhoto() {
        if (selectedPreviewUrl) {
            URL.revokeObjectURL(selectedPreviewUrl);
        }

        selectedFile = null;
        selectedPreviewUrl = null;
        editImageInput.value = '';

        previewImage.removeAttribute('src');
        previewImageBlur.removeAttribute('src');
        previewFrame.classList.add('hidden');
        uploadEmptyTile.classList.remove('hidden');

        selectedFileName.textContent = '-';
        selectedFileMeta.textContent = 'Siap diedit';

        showUploadMessage('Belum ada foto dipilih. Upload foto terlebih dahulu.');
        refreshFormState();
    }

    function setSelectedPhoto(file) {
        if (!file) return;

        if (!file.type || !file.type.startsWith('image/')) {
            showUploadMessage('File harus berupa gambar JPG, PNG, atau WEBP.', 'error');
            return;
        }

        if (selectedPreviewUrl) {
            URL.revokeObjectURL(selectedPreviewUrl);
        }

        selectedFile = file;
        selectedPreviewUrl = URL.createObjectURL(file);

        previewImage.src = selectedPreviewUrl;
        previewImageBlur.src = selectedPreviewUrl;
        previewFrame.classList.remove('hidden');
        uploadEmptyTile.classList.add('hidden');

        selectedFileName.textContent = file.name;
        selectedFileMeta.textContent = `${formatFileSize(file.size)} · Siap diedit`;

        showUploadMessage('Foto berhasil dimuat. Tulis instruksi edit, lalu klik Edit Foto Sekarang.', 'success');
        refreshFormState();
    }

    function showEmptyState(title, text, icon = 'photo_filter') {
        emptyStateTitle.textContent = title;
        emptyStateText.textContent = text;
        emptyStateIcon.textContent = icon;
        resultsEmptyState.classList.remove('hidden');
        resultsGrid.classList.add('hidden');
        resultsGrid.innerHTML = '';
        resultSubtitle.textContent = 'Hasil hanya muncul setelah proses edit berhasil.';
    }

    function showLoadingState() {
        resultsEmptyState.classList.add('hidden');
        resultsGrid.classList.remove('hidden');
        resultsGrid.innerHTML = '';
        resultSubtitle.textContent = 'AI sedang memproses variasi edit. Mohon tunggu sebentar.';

        for (let i = 0; i < selectedCount; i += 1) {
            const skeleton = document.createElement('div');
            skeleton.className = 'rounded-3xl border border-outline-variant bg-surface-container-lowest overflow-hidden animate-pulse shadow-sm';
            skeleton.style.aspectRatio = getAspectRatioValue();
            skeleton.innerHTML = `
                <div class="w-full h-full bg-gradient-to-br from-surface-container-low via-surface-container-high to-surface-container-low flex items-center justify-center">
                    <div class="w-12 h-12 rounded-full border-4 border-primary/20 border-t-primary animate-spin"></div>
                </div>
            `;
            resultsGrid.appendChild(skeleton);
        }
    }

    function makeOverlayButton(icon, label, href = null) {
        const element = href ? document.createElement('a') : document.createElement('button');
        element.className = 'w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 hover:scale-105 transition-all';
        element.setAttribute('aria-label', label);

        if (href) {
            element.href = href;

            if (label.toLowerCase().includes('lihat')) {
                element.target = '_blank';
                element.rel = 'noopener';
            } else {
                element.download = '';
            }
        } else {
            element.type = 'button';
        }

        element.innerHTML = `<span class="material-symbols-outlined">${icon}</span>`;

        return element;
    }

    function createResultCard(imageUrl, index) {
        const card = document.createElement('article');
        card.className = 'bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0_14px_40px_rgba(0,0,0,0.08)] border border-outline-variant group relative';
        card.style.aspectRatio = getAspectRatioValue();

        const image = document.createElement('img');
        image.className = 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105';
        image.alt = `Hasil edit ${index + 1}`;
        image.src = imageUrl;

        const badge = document.createElement('div');
        badge.className = 'absolute top-3 left-3 px-3 py-1 rounded-full bg-black/50 text-white text-[11px] font-bold backdrop-blur';
        badge.textContent = `Edit ${index + 1} · ${selectedRatio}`;

        const overlay = document.createElement('div');
        overlay.className = 'absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4';

        const actionRow = document.createElement('div');
        actionRow.className = 'flex justify-between items-center gap-3';

        const leftActions = document.createElement('div');
        leftActions.className = 'flex gap-2';
        leftActions.appendChild(makeOverlayButton('download', 'Download hasil', imageUrl));
        leftActions.appendChild(makeOverlayButton('zoom_in', 'Lihat detail', imageUrl));

        const rightActions = document.createElement('div');
        rightActions.className = 'flex gap-2';

        const regenerateButton = makeOverlayButton('refresh', 'Generate ulang');
        regenerateButton.addEventListener('click', handleEditPhoto);

        const favoriteButton = makeOverlayButton('favorite', 'Favorit');
        favoriteButton.addEventListener('click', () => {
            favoriteButton.classList.toggle('bg-white/40');
            favoriteButton.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' 1";
        });

        rightActions.appendChild(regenerateButton);
        rightActions.appendChild(favoriteButton);

        actionRow.appendChild(leftActions);
        actionRow.appendChild(rightActions);
        overlay.appendChild(actionRow);

        card.appendChild(image);
        card.appendChild(badge);
        card.appendChild(overlay);

        return card;
    }

    function normalizeGeneratedImages(responseData) {
        if (!responseData) return [];

        const possibleArrays = [
            responseData.images,
            responseData.results,
            responseData.urls,
            responseData.outputs,
            responseData.data?.images,
            responseData.data?.results,
            responseData.data?.urls,
            Array.isArray(responseData.data) ? responseData.data : null,
        ].filter(Boolean);

        let rawImages = possibleArrays.find(Array.isArray) || [];

        if (!rawImages.length) {
            const singleImage = responseData.image_url || responseData.image || responseData.url || responseData.output_url || responseData.output;
            rawImages = singleImage ? [singleImage] : [];
        }

        return rawImages
            .map((item) => {
                if (typeof item === 'string') return item;
                return item?.url || item?.image_url || item?.src || item?.output_url || item?.path || null;
            })
            .filter(Boolean)
            .slice(0, selectedCount);
    }

    function renderGeneratedImages(imageUrls) {
        const validImages = imageUrls.filter(Boolean).slice(0, selectedCount);

        if (validImages.length === 0) {
            showEmptyState('Belum ada gambar dari server', 'Proses selesai, tetapi API belum mengembalikan URL gambar hasil edit.', 'image_not_supported');
            return;
        }

        resultsEmptyState.classList.add('hidden');
        resultsGrid.classList.remove('hidden');
        resultsGrid.innerHTML = '';
        resultSubtitle.textContent = `${validImages.length} hasil edit berhasil dibuat.`;

        validImages.forEach((imageUrl, index) => {
            resultsGrid.appendChild(createResultCard(imageUrl, index));
        });
    }

    async function requestEditPhoto() {
        const formData = new FormData();
        formData.append('image', selectedFile);
        formData.append('prompt', promptInput.value.trim());
        formData.append('ratio', selectedRatio);
        formData.append('count', String(selectedCount));

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(EDIT_ENDPOINT, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Endpoint edit belum tersedia atau terjadi error di server. Sesuaikan route /edit/generate di backend kamu.');
        }

        const data = await response.json();
        return normalizeGeneratedImages(data);
    }

    async function handleEditPhoto() {
        if (!selectedFile) {
            showUploadMessage('Upload foto terlebih dahulu sebelum edit.', 'error');
            return;
        }

        isProcessing = true;
        refreshFormState();
        editButton.classList.add('opacity-70', 'cursor-not-allowed');
        editButtonIcon.textContent = 'progress_activity';
        editButtonIcon.classList.add('animate-spin');
        editButtonText.textContent = 'Memproses...';
        showLoadingState();

        try {
            const generatedImages = await requestEditPhoto();
            renderGeneratedImages(generatedImages);
        } catch (error) {
            showEmptyState('Edit belum berhasil', error.message || 'Terjadi kesalahan saat memproses foto.', 'warning');
        } finally {
            isProcessing = false;
            editButton.classList.remove('opacity-70', 'cursor-not-allowed');
            editButtonIcon.textContent = 'auto_fix_high';
            editButtonIcon.classList.remove('animate-spin');
            editButtonText.textContent = 'Edit Foto Sekarang';
            refreshFormState();
        }
    }

    function appendPromptText(text) {
        const current = promptInput.value.trim();
        promptInput.value = current ? `${current}\n${text}` : text;
        promptInput.focus();
        updatePromptCounter();
    }

    function enhancePrompt() {
        const currentPrompt = promptInput.value.trim();
        const professionalPrompt = 'Edit foto agar terlihat profesional, natural, dan realistis. Pertahankan identitas subjek, rapikan detail yang mengganggu, tingkatkan pencahayaan, warna, ketajaman, dan komposisi. Hasil akhir harus clean, high detail, dan siap dipakai untuk konten profesional.';

        promptInput.value = currentPrompt
            ? `${currentPrompt}\n\nTingkatkan dengan gaya profesional: pertahankan identitas subjek, lighting cinematic, warna harmonis, detail tajam, background rapi, dan hasil realistis.`
            : professionalPrompt;

        updatePromptCounter();
        promptInput.focus();
    }

    uploadDropArea.addEventListener('click', (event) => {
        if (event.target.closest('#removePhotoButton')) return;
        openFilePicker();
    });

    choosePhotoButton.addEventListener('click', (event) => {
        event.stopPropagation();
        openFilePicker();
    });

    replacePhotoButton.addEventListener('click', (event) => {
        event.stopPropagation();
        openFilePicker();
    });

    removePhotoButton.addEventListener('click', (event) => {
        event.stopPropagation();
        clearSelectedPhoto();
    });

    editImageInput.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        setSelectedPhoto(file);
        editImageInput.value = '';
    });

    uploadDropArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadDropArea.classList.add('is-dragging');
    });

    ['dragleave', 'dragend'].forEach((eventName) => {
        uploadDropArea.addEventListener(eventName, () => {
            uploadDropArea.classList.remove('is-dragging');
        });
    });

    uploadDropArea.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadDropArea.classList.remove('is-dragging');
        const file = event.dataTransfer.files?.[0];
        setSelectedPhoto(file);
    });

    promptInput.addEventListener('input', updatePromptCounter);
    enhancePromptButton.addEventListener('click', enhancePrompt);
    promptChips.forEach((chip) => {
        chip.addEventListener('click', () => appendPromptText(chip.dataset.promptChip));
    });

    ratioButtons.forEach((button) => {
        button.addEventListener('click', () => {
            selectedRatio = button.dataset.ratio;
            selectedRatioLabel = button.dataset.label || selectedRatio;
            setButtonActive(ratioButtons, button);
            updateOutputMeta();
        });
    });

    countButtons.forEach((button) => {
        button.addEventListener('click', () => {
            selectedCount = Number(button.dataset.count);
            setButtonActive(countButtons, button);
            updateOutputMeta();
        });
    });

    editButton.addEventListener('click', handleEditPhoto);

    window.addEventListener('beforeunload', () => {
        if (selectedPreviewUrl) {
            URL.revokeObjectURL(selectedPreviewUrl);
        }
    });

    updatePromptCounter();
    updateOutputMeta();
    clearSelectedPhoto();
    showEmptyState('Belum ada hasil edit', 'Upload foto, isi prompt edit, pilih rasio dan jumlah hasil, lalu klik tombol Edit Foto Sekarang.');
});
</script>
@endsection
