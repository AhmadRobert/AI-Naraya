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
                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">edit_note</span>
                            AI Copywriter
                        </div>

                        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Buat Caption</h1>
                        <p class="mt-2 text-body-md text-on-surface-variant">Masukkan ide/topik produk, pilih gaya bahasa dan panjang, lalu generate caption siap pakai.</p>
                    </div>

                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container items-center justify-center shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">campaign</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-7">
                <!-- 1. Ide / Topik -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">1</span>
                            Ide / Topik
                        </h2>
                        <button id="enhancePromptButton" class="text-primary flex items-center gap-1 hover:underline text-label-sm font-bold" type="button">
                            <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                            Enhance Prompt
                        </button>
                    </div>

                    <div class="relative">
                        <textarea
                            id="promptInput"
                            maxlength="5000"
                            class="w-full h-36 p-4 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md resize-none outline-none placeholder:text-on-surface-variant/60"
                            placeholder="Contoh: Promo internet fiber 50 Mbps harga Rp150rb/bulan untuk pelanggan baru"
                        ></textarea>
                    </div>
                </section>

                <!-- 2. Gaya Bahasa -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">2</span>
                            Gaya Bahasa
                        </h2>
                        <span id="styleHint" class="text-label-sm text-on-surface-variant font-medium">Default</span>
                    </div>

                    <div id="styleGroup" class="grid grid-cols-5 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-style="default" data-label="Default" class="style-button rounded-xl py-2.5 px-1 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-sm transition-all">Default</button>
                        <button type="button" data-style="santai" data-label="Santai" class="style-button rounded-xl py-2.5 px-1 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-sm transition-all">Santai</button>
                        <button type="button" data-style="formal" data-label="Formal" class="style-button rounded-xl py-2.5 px-1 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-sm transition-all">Formal</button>
                        <button type="button" data-style="persuasif" data-label="Persuasif" class="style-button rounded-xl py-2.5 px-1 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-sm transition-all">Persuasif</button>
                        <button type="button" data-style="humoris" data-label="Humoris" class="style-button rounded-xl py-2.5 px-1 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-sm transition-all">Humoris</button>
                    </div>
                </section>

                <!-- 3. Panjang Caption -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">3</span>
                            Panjang Caption
                        </h2>
                        <span class="text-label-sm text-on-surface-variant font-medium">± <span id="lengthEstimate" class="text-primary font-bold">100</span> kata</span>
                    </div>

                    <div id="lengthGroup" class="grid grid-cols-4 gap-1 p-1 bg-surface-container-low rounded-2xl border border-outline-variant/50">
                        <button type="button" data-length="60" class="length-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">Singkat</button>
                        <button type="button" data-length="100" class="length-button rounded-xl py-2.5 bg-primary-container text-on-primary-container shadow-sm font-label-md text-label-md transition-all">Sedang</button>
                        <button type="button" data-length="150" class="length-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">Panjang</button>
                        <button type="button" data-length="250" class="length-button rounded-xl py-2.5 text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">Ekstra</button>
                    </div>
                </section>

                <!-- CTA -->
                <button id="generateButton" class="relative w-full overflow-hidden py-4 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl font-headline-md text-headline-md flex items-center justify-center gap-3 hover:brightness-105 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none" type="button">
                    <span class="absolute inset-0 caption-shine opacity-40"></span>
                    <span id="generateButtonIcon" class="material-symbols-outlined relative" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                    <span id="generateButtonText" class="relative">Generate Caption</span>
                </button>

                <div class="flex items-start gap-2">
                    <span id="statusIcon" class="material-symbols-outlined text-[18px] text-on-surface-variant mt-0.5">info</span>
                    <p id="statusText" class="text-label-sm text-on-surface-variant">Isi ide/topik terlebih dahulu, minimal beberapa kata.</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- RIGHT PANEL -->
    <main class="xl:col-span-7 2xl:col-span-8 min-w-0">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-label-sm font-bold mb-3">
                    <span class="material-symbols-outlined text-[16px]">description</span>
                    Preview Output
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span id="outputStyleBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">Gaya: Default</span>
                <span id="outputLengthBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">± 100 kata</span>
            </div>
        </div>

        <section class="rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/80 shadow-[0_18px_60px_rgba(0,0,0,0.06)] p-4 sm:p-6 min-h-[560px] backdrop-blur">
            <div id="resultsEmptyState" class="min-h-[510px] rounded-3xl border-2 border-dashed border-outline-variant/80 bg-gradient-to-br from-surface-container-lowest via-surface-container-low to-primary-container/10 flex flex-col items-center justify-center text-center p-8 overflow-hidden relative">
                <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-primary-container/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-56 h-56 rounded-full bg-primary/10 blur-3xl"></div>
                <div class="relative w-20 h-20 rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-lg flex items-center justify-center mb-5">
                    <span id="emptyStateIcon" class="material-symbols-outlined text-5xl text-primary">edit_note</span>
                </div>
                <h3 id="emptyStateTitle" class="relative font-headline-md text-on-surface mb-2">Belum ada caption</h3>
                <p id="emptyStateText" class="relative text-body-md text-on-surface-variant max-w-md leading-relaxed">Isi ide/topik, pilih gaya bahasa dan panjang, lalu klik tombol Generate Caption.</p>

                <div class="relative mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Hook</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Caption</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">CTA</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Hashtag</span>
                </div>
            </div>

            <div id="resultCard" class="hidden rounded-3xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-[0_10px_30px_rgba(0,0,0,0.05)]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1;">edit_note</span>
                        Hasil Caption
                    </h3>
                    <button id="copyButton" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-outline-variant font-bold text-on-surface hover:bg-surface-container-high transition-all text-label-sm">
                        <span class="material-symbols-outlined text-[16px]">content_copy</span>
                        Copy
                    </button>
                </div>
                <div id="resultOutput" class="rounded-2xl bg-surface-container-low border border-outline-variant/60 p-5 text-body-md text-on-surface whitespace-pre-wrap leading-relaxed"></div>
            </div>
        </section>
    </main>
</div>

<style>
    .caption-shine::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 45%;
        height: 320%;
        transform: rotate(25deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
        animation: captionShine 3.8s ease-in-out infinite;
    }

    @keyframes captionShine {
        0%, 45% { left: -50%; }
        100% { left: 130%; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const GENERATE_ENDPOINT = @json(url('/caption/generate'));

    const promptInput = document.getElementById('promptInput');
    const enhancePromptButton = document.getElementById('enhancePromptButton');

    const styleButtons = document.querySelectorAll('.style-button');
    const styleHint = document.getElementById('styleHint');
    const outputStyleBadge = document.getElementById('outputStyleBadge');

    const lengthButtons = document.querySelectorAll('.length-button');
    const lengthEstimate = document.getElementById('lengthEstimate');
    const outputLengthBadge = document.getElementById('outputLengthBadge');

    const generateButton = document.getElementById('generateButton');
    const generateButtonIcon = document.getElementById('generateButtonIcon');
    const generateButtonText = document.getElementById('generateButtonText');

    const statusIcon = document.getElementById('statusIcon');
    const statusText = document.getElementById('statusText');

    const resultsEmptyState = document.getElementById('resultsEmptyState');
    const emptyStateIcon = document.getElementById('emptyStateIcon');
    const emptyStateTitle = document.getElementById('emptyStateTitle');
    const emptyStateText = document.getElementById('emptyStateText');
    const resultCard = document.getElementById('resultCard');
    const resultOutput = document.getElementById('resultOutput');
    const copyButton = document.getElementById('copyButton');

    const activeClasses = ['bg-primary-container', 'text-on-primary-container', 'shadow-sm'];
    const inactiveClasses = ['text-on-surface-variant', 'hover:bg-surface-container-high'];

    let selectedStyle = 'default';
    let selectedStyleLabel = 'Default';
    let selectedLength = 100;

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

    function updateOutputMeta() {
        styleHint.textContent = selectedStyleLabel;
        lengthEstimate.textContent = selectedLength;
        outputStyleBadge.textContent = `Gaya: ${selectedStyleLabel}`;
        outputLengthBadge.textContent = `± ${selectedLength} kata`;
    }

    function showStatus(message, type = 'info') {
        statusText.textContent = message;
        statusText.className = 'text-label-sm';
        statusIcon.className = 'material-symbols-outlined text-[18px] mt-0.5';

        if (type === 'error') {
            statusText.classList.add('text-red-600');
            statusIcon.classList.add('text-red-600');
            statusIcon.textContent = 'error';
            return;
        }

        statusText.classList.add('text-on-surface-variant');
        statusIcon.classList.add('text-on-surface-variant');
        statusIcon.textContent = 'info';
    }

    function showEmptyState(title, text) {
        resultCard.classList.add('hidden');
        resultsEmptyState.classList.remove('hidden');
        emptyStateIcon.textContent = 'edit_note';
        emptyStateTitle.textContent = title;
        emptyStateText.textContent = text;
    }

    function showLoadingState() {
        resultCard.classList.add('hidden');
        resultsEmptyState.classList.remove('hidden');
        emptyStateIcon.textContent = 'progress_activity';
        emptyStateIcon.classList.add('animate-spin');
        emptyStateTitle.textContent = 'Sedang membuat caption...';
        emptyStateText.textContent = 'Mohon tunggu sebentar, AI sedang menyusun hook, caption, CTA, dan hashtag.';
    }

    function showResult(text) {
        emptyStateIcon.classList.remove('animate-spin');
        resultsEmptyState.classList.add('hidden');
        resultCard.classList.remove('hidden');
        resultOutput.textContent = text;
    }

    styleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            selectedStyle = button.dataset.style;
            selectedStyleLabel = button.dataset.label || selectedStyle;
            setButtonActive(styleButtons, button);
            updateOutputMeta();
        });
    });

    lengthButtons.forEach((button) => {
        button.addEventListener('click', () => {
            selectedLength = Number(button.dataset.length);
            setButtonActive(lengthButtons, button);
            updateOutputMeta();
        });
    });

    enhancePromptButton.addEventListener('click', () => {
        const basePrompt = promptInput.value.trim();
        const suggestion = 'Sertakan manfaat utama untuk pelanggan, satu angka/data konkret (harga, kecepatan, durasi promo), dan kesan urgensi.';
        promptInput.value = basePrompt
            ? `${basePrompt}\n\nTingkatkan dengan: ${suggestion}`
            : suggestion;
        promptInput.focus();
    });

    const STATUS_ENDPOINT_BASE = @json(url('/caption/status'));

    async function pollCaptionStatus(promptId) {
        return new Promise((resolve, reject) => {
            const check = async () => {
                try {
                    const response = await fetch(`${STATUS_ENDPOINT_BASE}/${promptId}`);
                    const data = await response.json().catch(() => ({}));

                    if (data.status === 'done' && data.caption) {
                        resolve(data.caption);
                        return;
                    }

                    if (data.status === 'failed' || (data.success === false && data.message)) {
                        reject(new Error(data.message || 'Proses pembuatan caption gagal di ComfyUI.'));
                        return;
                    }

                    // Masih diproses, lanjut cek terus terusan tiap 2 detik
                    setTimeout(check, 2000);
                } catch (error) {
                    // Jika ada guncangan jaringan sementara, coba cek lagi 2 detik kemudian
                    setTimeout(check, 2000);
                }
            };

            check();
        });
    }

    async function requestGenerateCaption() {
        const formData = new FormData();
        formData.append('prompt', promptInput.value.trim());
        formData.append('style', selectedStyle);
        formData.append('length', selectedLength);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(GENERATE_ENDPOINT, {
            method: 'POST',
            headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
            body: formData,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data.success) {
            throw new Error(data.message || `Generate gagal dikirim (HTTP ${response.status}).`);
        }

        // Jika caption langsung tersedia
        if (data.caption) {
            return data.caption;
        }

        // Jika mendapatkan prompt_id, lakukan loop polling ke status endpoint sampai output selesai
        if (data.prompt_id) {
            showStatus('Prompt berhasil terkirim. Memeriksa hasil ke ComfyUI secara berkesinambungan...', 'info');
            return await pollCaptionStatus(data.prompt_id);
        }

        throw new Error('Hasil caption tidak ditemukan.');
    }

    async function handleGenerate() {
        const idea = promptInput.value.trim();

        if (!idea) {
            showStatus('Isi ide/topik terlebih dahulu.', 'error');
            promptInput.focus();
            return;
        }

        generateButton.disabled = true;
        generateButton.classList.add('opacity-70', 'cursor-not-allowed');
        generateButtonIcon.textContent = 'progress_activity';
        generateButtonIcon.classList.add('animate-spin');
        generateButtonText.textContent = 'Memproses...';
        showStatus('Sedang menghubungi AI...', 'info');
        showLoadingState();

        try {
            const caption = await requestGenerateCaption();
            showResult(caption);
            showStatus('Caption berhasil dibuat.', 'info');
        } catch (error) {
            showEmptyState('Generate belum berhasil', error.message || 'Terjadi kesalahan saat membuat caption.');
            showStatus(error.message || 'Terjadi kesalahan.', 'error');
        } finally {
            generateButton.disabled = false;
            generateButton.classList.remove('opacity-70', 'cursor-not-allowed');
            generateButtonIcon.textContent = 'auto_awesome';
            generateButtonIcon.classList.remove('animate-spin');
            generateButtonText.textContent = 'Generate Caption';
        }
    }

    copyButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(resultOutput.textContent);
            copyButton.innerHTML = '<span class="material-symbols-outlined text-[16px]">check</span> Tersalin!';
            setTimeout(() => {
                copyButton.innerHTML = '<span class="material-symbols-outlined text-[16px]">content_copy</span> Copy';
            }, 1800);
        } catch (error) {
            alert('Gagal menyalin ke clipboard.');
        }
    });

    generateButton.addEventListener('click', handleGenerate);

    updateOutputMeta();
});
</script>
@endsection