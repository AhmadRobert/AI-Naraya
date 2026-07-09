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
                            AI Image Composer
                        </div>
                        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Gabungkan Foto</h1>
                        <p class="mt-2 text-body-md text-on-surface-variant">Unggah 2–5 gambar, beri arahan, lalu buat variasi visual yang rapi.</p>
                    </div>
                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container items-center justify-center shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">photo_library</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-7">
                <!-- 1. Upload Section -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">1</span>
                            Unggah Gambar
                        </h2>
                        <span id="imageCounterBadge" class="px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold">0/5</span>
                    </div>

                    <input id="imageUploadInput" class="hidden" type="file" accept="image/*" multiple>

                    <div id="uploadDropZone" class="upload-zone rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-low/70 p-4 transition-all cursor-pointer">
                        <div id="uploadPreviewContainer" class="min-h-[176px] grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <!-- Preview akan dirender lewat JavaScript -->
                        </div>
                    </div>

                    <div class="mt-3 flex items-start gap-2">
                        <span id="uploadStatusIcon" class="material-symbols-outlined text-[18px] text-on-surface-variant mt-0.5">info</span>
                        <p id="uploadHelperText" class="text-label-sm text-on-surface-variant">Belum ada gambar dipilih. Unggah minimal 2 gambar.</p>
                    </div>
                </section>

                <!-- 2. Instructions Section -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">2</span>
                            Instruksi
                        </h2>
                        <button id="enhancePromptButton" class="text-primary flex items-center gap-1 hover:underline text-label-sm font-bold" type="button">
                            <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                            Enhance Prompt
                        </button>
                    </div>

                    <div class="relative">
                        <textarea
                            id="promptInput"
                            class="w-full h-36 p-4 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md resize-none outline-none placeholder:text-on-surface-variant/60"
                            placeholder="Contoh: gabungkan semua gambar menjadi satu komposisi cinematic, pencahayaan soft, warna harmonis, detail tajam, background bersih."
                        ></textarea>
                        <div class="absolute bottom-3 right-3 px-2 py-1 rounded-full bg-surface-container-lowest text-[11px] text-on-surface-variant border border-outline-variant/60">Opsional</div>
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
                <button id="generateButton" class="relative w-full overflow-hidden py-4 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl font-headline-md text-headline-md flex items-center justify-center gap-3 hover:brightness-105 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none" type="button">
                    <span class="absolute inset-0 gabung-shine opacity-40"></span>
                    <span id="generateButtonIcon" class="material-symbols-outlined relative" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                    <span id="generateButtonText" class="relative">Buat Variasi</span>
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
                    <span id="emptyStateIcon" class="material-symbols-outlined text-5xl text-primary">add_photo_alternate</span>
                </div>
                <h3 id="emptyStateTitle" class="relative font-headline-md text-on-surface mb-2">Belum ada hasil generasi</h3>
                <p id="emptyStateText" class="relative text-body-md text-on-surface-variant max-w-md leading-relaxed">Unggah minimal 2 gambar, pilih rasio dan jumlah hasil, lalu klik tombol Buat Variasi.</p>

                <div class="relative mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Min 2 gambar</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Maks 5 gambar</span>
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

    .gabung-shine::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 45%;
        height: 320%;
        transform: rotate(25deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
        animation: gabungShine 3.8s ease-in-out infinite;
    }

    @keyframes gabungShine {
        0%, 45% { left: -50%; }
        100% { left: 130%; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX_IMAGES = 5;
    const MIN_IMAGES = 2;
    const GENERATE_ENDPOINT = @json(url('/gabung/generate'));

    const imageUploadInput = document.getElementById('imageUploadInput');
    const uploadDropZone = document.getElementById('uploadDropZone');
    const uploadPreviewContainer = document.getElementById('uploadPreviewContainer');
    const uploadHelperText = document.getElementById('uploadHelperText');
    const uploadStatusIcon = document.getElementById('uploadStatusIcon');
    const imageCounterBadge = document.getElementById('imageCounterBadge');
    const promptInput = document.getElementById('promptInput');
    const enhancePromptButton = document.getElementById('enhancePromptButton');
    const ratioButtons = document.querySelectorAll('.ratio-button');
    const countButtons = document.querySelectorAll('.count-button');
    const ratioHint = document.getElementById('ratioHint');
    const creditEstimate = document.getElementById('creditEstimate');
    const outputRatioBadge = document.getElementById('outputRatioBadge');
    const outputCountBadge = document.getElementById('outputCountBadge');
    const generateButton = document.getElementById('generateButton');
    const generateButtonIcon = document.getElementById('generateButtonIcon');
    const generateButtonText = document.getElementById('generateButtonText');
    const resultsEmptyState = document.getElementById('resultsEmptyState');
    const emptyStateIcon = document.getElementById('emptyStateIcon');
    const emptyStateTitle = document.getElementById('emptyStateTitle');
    const emptyStateText = document.getElementById('emptyStateText');
    const resultsGrid = document.getElementById('resultsGrid');

    const activeClasses = ['bg-primary-container', 'text-on-primary-container', 'shadow-sm'];
    const inactiveClasses = ['text-on-surface-variant', 'hover:bg-surface-container-high'];

    let uploadedImages = [];
    let selectedRatio = '1:1';
    let selectedRatioLabel = 'Square';
    let selectedCount = 4;

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

    function showUploadMessage(message, type = 'info') {
        uploadHelperText.textContent = message;
        uploadHelperText.className = 'text-label-sm';
        uploadStatusIcon.className = 'material-symbols-outlined text-[18px] mt-0.5';

        if (type === 'error') {
            uploadHelperText.classList.add('text-red-600');
            uploadStatusIcon.classList.add('text-red-600');
            uploadStatusIcon.textContent = 'error';
            return;
        }

        if (type === 'success') {
            uploadHelperText.classList.add('text-primary');
            uploadStatusIcon.classList.add('text-primary');
            uploadStatusIcon.textContent = 'check_circle';
            return;
        }

        uploadHelperText.classList.add('text-on-surface-variant');
        uploadStatusIcon.classList.add('text-on-surface-variant');
        uploadStatusIcon.textContent = 'info';
    }

    function refreshFormState() {
        const isReady = uploadedImages.length >= MIN_IMAGES;
        imageCounterBadge.textContent = `${uploadedImages.length}/${MAX_IMAGES}`;
        imageCounterBadge.className = isReady
            ? 'px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-sm font-bold'
            : 'px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant text-label-sm font-bold';

        generateButton.disabled = !isReady;
    }

    function renderEmptyUploadTile() {
        uploadPreviewContainer.className = 'min-h-[176px] grid grid-cols-1 gap-3';
        uploadPreviewContainer.innerHTML = `
            <button id="addImageButton" type="button" class="group min-h-[176px] rounded-2xl border border-outline-variant/50 bg-surface-container-lowest/70 flex flex-col items-center justify-center text-center p-5 hover:bg-white hover:border-primary transition-all">
                <span class="w-14 h-14 rounded-2xl bg-primary-container text-on-primary-container flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                </span>
                <span class="font-bold text-on-surface">Klik atau drag gambar ke sini</span>
                <span class="text-label-sm text-on-surface-variant mt-1">PNG, JPG, WEBP · minimal 2 gambar</span>
            </button>
        `;
    }

    function renderUploadPreviews() {
        uploadPreviewContainer.innerHTML = '';

        if (uploadedImages.length === 0) {
            renderEmptyUploadTile();
            showUploadMessage('Belum ada gambar dipilih. Unggah minimal 2 gambar.');
            refreshFormState();
            return;
        }

        uploadPreviewContainer.className = 'min-h-[176px] grid grid-cols-2 sm:grid-cols-3 gap-3';

        uploadedImages.forEach((item, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative aspect-square rounded-2xl overflow-hidden border border-outline-variant bg-surface-container-lowest group shadow-sm';

            const image = document.createElement('img');
            image.className = 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-105';
            image.alt = `Gambar unggahan ${index + 1}`;
            image.src = item.url;

            const numberBadge = document.createElement('span');
            numberBadge.className = 'absolute left-2 top-2 w-7 h-7 rounded-full bg-black/55 text-white flex items-center justify-center text-[11px] font-bold backdrop-blur';
            numberBadge.textContent = index + 1;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center shadow opacity-95 hover:scale-105 transition-transform';
            removeButton.setAttribute('aria-label', `Hapus gambar ${index + 1}`);
            removeButton.innerHTML = '<span class="material-symbols-outlined text-[16px]">close</span>';
            removeButton.addEventListener('click', (event) => {
                event.stopPropagation();
                removeUploadedImage(index);
            });

            wrapper.appendChild(image);
            wrapper.appendChild(numberBadge);
            wrapper.appendChild(removeButton);
            uploadPreviewContainer.appendChild(wrapper);
        });

        if (uploadedImages.length < MAX_IMAGES) {
            const addButton = document.createElement('button');
            addButton.id = 'addImageButton';
            addButton.type = 'button';
            addButton.className = 'aspect-square rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-lowest/70 flex flex-col items-center justify-center hover:border-primary hover:bg-primary-container/10 transition-all';
            addButton.innerHTML = `
                <span class="material-symbols-outlined text-3xl text-primary">add</span>
                <span class="text-[11px] font-bold text-on-surface mt-1">TAMBAH</span>
            `;
            uploadPreviewContainer.appendChild(addButton);
        }

        if (uploadedImages.length < MIN_IMAGES) {
            showUploadMessage(`Baru ${uploadedImages.length} gambar. Tambahkan ${MIN_IMAGES - uploadedImages.length} gambar lagi.`, 'error');
        } else {
            showUploadMessage(`${uploadedImages.length} gambar siap digabungkan. Maksimal ${MAX_IMAGES} gambar.`, 'success');
        }

        refreshFormState();
    }

    function removeUploadedImage(index) {
        if (!uploadedImages[index]) return;
        URL.revokeObjectURL(uploadedImages[index].url);
        uploadedImages.splice(index, 1);
        renderUploadPreviews();
    }

    function addUploadedFiles(files) {
        const selectedFiles = Array.from(files || []);
        const imageFiles = selectedFiles.filter((file) => file.type.startsWith('image/'));

        if (imageFiles.length === 0) {
            showUploadMessage('File yang dipilih harus berupa gambar.', 'error');
            return;
        }

        const availableSlots = MAX_IMAGES - uploadedImages.length;

        if (availableSlots <= 0) {
            showUploadMessage(`Maksimal hanya ${MAX_IMAGES} gambar. Hapus salah satu gambar untuk mengganti.`, 'error');
            return;
        }

        imageFiles.slice(0, availableSlots).forEach((file) => {
            uploadedImages.push({
                file,
                url: URL.createObjectURL(file),
            });
        });

        renderUploadPreviews();

        if (imageFiles.length > availableSlots) {
            showUploadMessage(`Maksimal ${MAX_IMAGES} gambar. Sebagian file tidak ditambahkan.`, 'error');
        }

        imageUploadInput.value = '';
    }

    function showEmptyState(title, text, icon = 'add_photo_alternate') {
        emptyStateTitle.textContent = title;
        emptyStateText.textContent = text;
        emptyStateIcon.textContent = icon;
        resultsEmptyState.classList.remove('hidden');
        resultsGrid.classList.add('hidden');
        resultsGrid.innerHTML = '';
    }

    function showLoadingState() {
        resultsEmptyState.classList.add('hidden');
        resultsGrid.classList.remove('hidden');
        resultsGrid.innerHTML = '';

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
        image.alt = `Hasil generasi ${index + 1}`;
        image.src = imageUrl;

        const badge = document.createElement('div');
        badge.className = 'absolute top-3 left-3 px-3 py-1 rounded-full bg-black/50 text-white text-[11px] font-bold backdrop-blur';
        badge.textContent = `Hasil ${index + 1} · ${selectedRatio}`;

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
        rightActions.appendChild(makeOverlayButton('refresh', 'Generate ulang'));
        const favoriteButton = makeOverlayButton('favorite', 'Favorit');
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

    function normalizeGeneratedImages(responseData) {
        const rawImages = responseData?.images || responseData?.results || responseData?.data || [];
        const imageList = Array.isArray(rawImages) ? rawImages : [rawImages];

        return imageList
            .map((item) => {
                if (typeof item === 'string') return item;
                return item?.url || item?.image_url || item?.src || item?.path || null;
            })
            .filter(Boolean);
    }

    function renderGeneratedImages(imageUrls) {
        const validImages = imageUrls.filter(Boolean).slice(0, selectedCount);

        if (validImages.length === 0) {
            showEmptyState('Belum ada gambar dari server', 'Proses generate selesai, tetapi API belum mengembalikan URL gambar hasil.', 'image_not_supported');
            return;
        }

        resultsEmptyState.classList.add('hidden');
        resultsGrid.classList.remove('hidden');
        resultsGrid.innerHTML = '';

        validImages.forEach((imageUrl, index) => {
            resultsGrid.appendChild(createResultCard(imageUrl, index));
        });
    }

    async function requestGenerateImages() {
        const formData = new FormData();

        uploadedImages.forEach((item) => {
            formData.append('images[]', item.file);
        });

        formData.append('prompt', promptInput.value.trim());
        formData.append('ratio', selectedRatio);
        formData.append('count', selectedCount);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(GENERATE_ENDPOINT, {
            method: 'POST',
            headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
            body: formData,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || `Generate gagal (HTTP ${response.status}).`);
        }

        return normalizeGeneratedImages(data);
    }

    async function handleGenerate() {
        if (uploadedImages.length < MIN_IMAGES) {
            showUploadMessage(`Unggah minimal ${MIN_IMAGES} gambar terlebih dahulu.`, 'error');
            return;
        }

        generateButton.disabled = true;
        generateButton.classList.add('opacity-70', 'cursor-not-allowed');
        generateButtonIcon.textContent = 'progress_activity';
        generateButtonIcon.classList.add('animate-spin');
        generateButtonText.textContent = 'Memproses...';
        showLoadingState();

        try {
            const generatedImages = await requestGenerateImages();
            renderGeneratedImages(generatedImages);
        } catch (error) {
            showEmptyState('Generate belum berhasil', error.message || 'Terjadi kesalahan saat membuat variasi gambar.', 'warning');
        } finally {
            generateButton.disabled = uploadedImages.length < MIN_IMAGES;
            generateButton.classList.remove('opacity-70', 'cursor-not-allowed');
            generateButtonIcon.textContent = 'auto_awesome';
            generateButtonIcon.classList.remove('animate-spin');
            generateButtonText.textContent = 'Buat Variasi';
        }
    }

    function openFilePicker() {
        if (uploadedImages.length >= MAX_IMAGES) {
            showUploadMessage(`Maksimal hanya ${MAX_IMAGES} gambar. Hapus salah satu gambar untuk mengganti.`, 'error');
            return;
        }

        imageUploadInput.click();
    }

    uploadDropZone.addEventListener('click', (event) => {
        if (event.target.closest('button[aria-label^="Hapus"]')) return;
        openFilePicker();
    });

    uploadDropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadDropZone.classList.add('is-dragging');
    });

    ['dragleave', 'dragend'].forEach((eventName) => {
        uploadDropZone.addEventListener(eventName, () => {
            uploadDropZone.classList.remove('is-dragging');
        });
    });

    uploadDropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadDropZone.classList.remove('is-dragging');
        addUploadedFiles(event.dataTransfer.files);
    });

    imageUploadInput.addEventListener('change', (event) => {
        addUploadedFiles(event.target.files);
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

    enhancePromptButton.addEventListener('click', () => {
        const basePrompt = promptInput.value.trim();
        const professionalPrompt = 'Gabungkan semua gambar menjadi satu komposisi visual yang natural, proporsional, cinematic, pencahayaan soft premium, warna harmonis, detail tajam, tanpa distorsi wajah/objek, background bersih, hasil terlihat realistis dan profesional.';
        promptInput.value = basePrompt
            ? `${basePrompt}\n\nTingkatkan dengan gaya profesional: komposisi natural, pencahayaan cinematic, warna harmonis, detail tajam, dan hasil realistis.`
            : professionalPrompt;
        promptInput.focus();
    });

    generateButton.addEventListener('click', handleGenerate);

    window.addEventListener('beforeunload', () => {
        uploadedImages.forEach((item) => URL.revokeObjectURL(item.url));
    });

    renderUploadPreviews();
    updateOutputMeta();
    showEmptyState('Belum ada hasil generasi', 'Unggah minimal 2 gambar, pilih rasio dan jumlah hasil, lalu klik tombol Buat Variasi.');
});
</script>
@endsection
