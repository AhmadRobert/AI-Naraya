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
                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">movie</span>
                            AI Storyboard Writer
                        </div>

                        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Storyboard Video</h1>
                        <p class="mt-2 text-body-md text-on-surface-variant">Masukkan ide iklan (boleh sebutkan durasinya, mis. 15 detik atau 30 detik). AI akan menentukan sendiri jumlah scene yang paling sesuai, lengkap dengan Visual, Camera, dan Mood.</p>
                    </div>

                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary-container text-on-primary-container items-center justify-center shadow-lg shadow-primary/10">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">movie_creation</span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-7">
                <!-- 1. Ide Iklan -->
                <section>
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-sm font-bold">1</span>
                            Ide Iklan
                        </h2>
                        <button id="enhancePromptButton" class="text-primary flex items-center gap-1 hover:underline text-label-sm font-bold" type="button">
                            <span class="material-symbols-outlined text-[16px]">auto_fix_high</span>
                            Enhance Prompt
                        </button>
                    </div>

                    <div class="relative">
                        <textarea
                            id="promptInput"
                            maxlength="10000"
                            class="w-full h-40 p-4 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-body-md text-body-md resize-none outline-none placeholder:text-on-surface-variant/60"
                            placeholder="Contoh: Iklan 15 detik untuk aplikasi kasir UMKM, target pemilik warung kecil"
                        ></textarea>
                    </div>

                    <!--
                        FIX: chip "Minimal 8 Scene" dihapus - jumlah scene
                        sekarang sepenuhnya mengikuti saran/jawaban terbaik
                        AI berdasarkan ide & durasi yang ditulis pengguna
                        di atas, bukan angka yang dipatok di UI.
                    -->
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="px-3 py-1.5 rounded-full bg-surface-container-low border border-outline-variant text-label-sm text-on-surface-variant flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                            Jumlah scene menyesuaikan
                        </span>
                        <span class="px-3 py-1.5 rounded-full bg-surface-container-low border border-outline-variant text-label-sm text-on-surface-variant">Visual · Camera · Mood</span>
                    </div>
                </section>

                <!-- CTA -->
                <button id="generateButton" class="relative w-full overflow-hidden py-4 bg-gradient-to-r from-primary to-primary-container text-white rounded-2xl font-headline-md text-headline-md flex items-center justify-center gap-3 hover:brightness-105 active:scale-[0.98] transition-all shadow-xl shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none" type="button">
                    <span class="absolute inset-0 storyboard-shine opacity-40"></span>
                    <span id="generateButtonIcon" class="material-symbols-outlined relative" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                    <span id="generateButtonText" class="relative">Generate Storyboard</span>
                </button>

                <div class="flex items-start gap-2">
                    <span id="statusIcon" class="material-symbols-outlined text-[18px] text-on-surface-variant mt-0.5">info</span>
                    <p id="statusText" class="text-label-sm text-on-surface-variant">Isi ide iklan terlebih dahulu (sebutkan durasi kalau ada).</p>
                </div>

                <!-- Lanjut ke Carousel -->
                <a href="{{ route('carousel') }}" class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl border border-outline-variant font-bold text-on-surface hover:bg-surface-container-high transition-all text-label-md">
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    Lanjutkan ke Carousel (render jadi gambar)
                </a>
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
                <span id="outputSceneBadge" class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">- Scene</span>
                <span class="px-3 py-2 rounded-xl bg-surface-container-lowest border border-outline-variant text-label-sm font-bold text-on-surface-variant">Storyboard Teks</span>
            </div>
        </div>

        <section class="rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/80 shadow-[0_18px_60px_rgba(0,0,0,0.06)] p-4 sm:p-6 min-h-[560px] backdrop-blur">
            <div id="resultsEmptyState" class="min-h-[510px] rounded-3xl border-2 border-dashed border-outline-variant/80 bg-gradient-to-br from-surface-container-lowest via-surface-container-low to-primary-container/10 flex flex-col items-center justify-center text-center p-8 overflow-hidden relative">
                <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-primary-container/20 blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-56 h-56 rounded-full bg-primary/10 blur-3xl"></div>
                <div class="relative w-20 h-20 rounded-3xl bg-surface-container-lowest border border-outline-variant shadow-lg flex items-center justify-center mb-5">
                    <span id="emptyStateIcon" class="material-symbols-outlined text-5xl text-primary">movie</span>
                </div>
                <h3 id="emptyStateTitle" class="relative font-headline-md text-on-surface mb-2">Belum ada storyboard</h3>
                <p id="emptyStateText" class="relative text-body-md text-on-surface-variant max-w-md leading-relaxed">Isi ide iklan (boleh sebutkan durasi seperti 15 detik atau 30 detik), lalu klik tombol Generate Storyboard. Jumlah scene menyesuaikan otomatis.</p>

                <div class="relative mt-6 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Scene</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Visual</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Camera</span>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 border border-outline-variant text-label-sm text-on-surface-variant">Mood</span>
                </div>
            </div>

            <div id="resultWrapper" class="hidden">
                <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
                    <h3 class="font-headline-md text-body-md font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1;">movie</span>
                        Hasil Storyboard
                    </h3>

                    <div class="flex items-center gap-2">
                        <!-- BARU: Export PDF -->
                        <button id="exportPdfButton" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-outline-variant font-bold text-on-surface hover:bg-surface-container-high transition-all text-label-sm">
                            <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                            Export PDF
                        </button>
                        <button id="copyButton" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-outline-variant font-bold text-on-surface hover:bg-surface-container-high transition-all text-label-sm">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            Copy Semua
                        </button>
                    </div>
                </div>

                <!-- BARU: banner panduan lanjut ke Carousel setelah export -->
                <div id="pdfExportedBanner" class="hidden mb-5 rounded-2xl border border-primary/30 bg-primary/5 p-4 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5">check_circle</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-body-md font-bold text-on-surface">PDF storyboard sudah diunduh</p>
                        <p class="text-body-sm text-on-surface-variant mt-0.5">Tidak perlu buat Word/PDF manual lagi - langsung upload file yang baru terunduh itu di fitur Carousel untuk dirender jadi gambar per-scene.</p>
                    </div>
                    <a href="{{ route('carousel') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white font-bold text-label-sm hover:brightness-105 transition-all">
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        Buka Carousel
                    </a>
                </div>

                <div id="resultsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-5"></div>

                <div id="rawFallback" class="hidden rounded-3xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-[0_10px_30px_rgba(0,0,0,0.05)]">
                    <div id="rawOutput" class="rounded-2xl bg-surface-container-low border border-outline-variant/60 p-5 text-body-md text-on-surface whitespace-pre-wrap leading-relaxed"></div>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
    .storyboard-shine::after {
        content: "";
        position: absolute;
        inset: -120% auto auto -30%;
        width: 45%;
        height: 320%;
        transform: rotate(25deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
        animation: storyboardShine 3.8s ease-in-out infinite;
    }

    @keyframes storyboardShine {
        0%, 45% { left: -50%; }
        100% { left: 130%; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const GENERATE_ENDPOINT = @json(url('/storyboard/generate'));
    const JSPDF_CDN_URL = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';

    const promptInput = document.getElementById('promptInput');
    const enhancePromptButton = document.getElementById('enhancePromptButton');

    const generateButton = document.getElementById('generateButton');
    const generateButtonIcon = document.getElementById('generateButtonIcon');
    const generateButtonText = document.getElementById('generateButtonText');

    const statusIcon = document.getElementById('statusIcon');
    const statusText = document.getElementById('statusText');
    const outputSceneBadge = document.getElementById('outputSceneBadge');

    const resultsEmptyState = document.getElementById('resultsEmptyState');
    const emptyStateIcon = document.getElementById('emptyStateIcon');
    const emptyStateTitle = document.getElementById('emptyStateTitle');
    const emptyStateText = document.getElementById('emptyStateText');

    const resultWrapper = document.getElementById('resultWrapper');
    const resultsGrid = document.getElementById('resultsGrid');
    const rawFallback = document.getElementById('rawFallback');
    const rawOutput = document.getElementById('rawOutput');
    const copyButton = document.getElementById('copyButton');
    const exportPdfButton = document.getElementById('exportPdfButton');
    const pdfExportedBanner = document.getElementById('pdfExportedBanner');

    let lastRawText = '';
    let jsPdfLoadPromise = null;

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
        resultWrapper.classList.add('hidden');
        resultsEmptyState.classList.remove('hidden');
        emptyStateIcon.classList.remove('animate-spin');
        emptyStateIcon.textContent = 'movie';
        emptyStateTitle.textContent = title;
        emptyStateText.textContent = text;
    }

    function showLoadingState() {
        resultWrapper.classList.add('hidden');
        resultsEmptyState.classList.remove('hidden');
        emptyStateIcon.textContent = 'progress_activity';
        emptyStateIcon.classList.add('animate-spin');
        emptyStateTitle.textContent = 'Sedang menyusun storyboard...';
        emptyStateText.textContent = 'Mohon tunggu sebentar, AI sedang menentukan jumlah scene yang paling sesuai untuk ide ini.';
    }

    // Parse teks hasil AI menjadi kartu per-scene (Scene / Visual / Camera / Mood)
    function parseScenes(text) {
        const blocks = text.split(/(?=Scene\s*\d+)/gi).map((b) => b.trim()).filter(Boolean);

        const scenes = blocks.map((block) => {
            const sceneMatch = block.match(/Scene\s*(\d+)/i);
            const visualMatch = block.match(/Visual\s*[:\-]\s*(.+)/i);
            const cameraMatch = block.match(/Camera\s*[:\-]\s*(.+)/i);
            const moodMatch = block.match(/Mood\s*[:\-]\s*(.+)/i);

            if (!sceneMatch) return null;

            return {
                scene: sceneMatch[1],
                visual: visualMatch ? visualMatch[1].trim() : '',
                camera: cameraMatch ? cameraMatch[1].trim() : '',
                mood: moodMatch ? moodMatch[1].trim() : '',
                raw: block,
            };
        }).filter(Boolean);

        return scenes;
    }

    function createSceneCard(scene) {
        const card = document.createElement('article');
        card.className = 'rounded-3xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-[0_10px_30px_rgba(0,0,0,0.05)] flex flex-col gap-3';

        card.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center text-label-sm font-extrabold">${scene.scene}</span>
                <span class="font-headline-md text-body-md font-bold text-on-surface">Scene ${scene.scene}</span>
            </div>
            ${scene.visual ? `<div><p class="text-label-sm font-bold text-on-surface-variant mb-0.5">Visual</p><p class="text-body-sm text-on-surface">${scene.visual}</p></div>` : ''}
            ${scene.camera ? `<div><p class="text-label-sm font-bold text-on-surface-variant mb-0.5">Camera</p><p class="text-body-sm text-on-surface">${scene.camera}</p></div>` : ''}
            ${scene.mood ? `<div><p class="text-label-sm font-bold text-on-surface-variant mb-0.5">Mood</p><p class="text-body-sm text-on-surface">${scene.mood}</p></div>` : ''}
        `;

        return card;
    }

    function showResult(text) {
        lastRawText = text;
        resultsEmptyState.classList.add('hidden');
        resultWrapper.classList.remove('hidden');
        resultsGrid.innerHTML = '';
        pdfExportedBanner.classList.add('hidden');

        const scenes = parseScenes(text);

        if (scenes.length >= 2) {
            resultsGrid.classList.remove('hidden');
            rawFallback.classList.add('hidden');
            scenes.forEach((scene) => resultsGrid.appendChild(createSceneCard(scene)));
            // FIX: label jumlah scene sekarang murni menampilkan hasil
            // ASLI dari AI (bisa 4, 6, 9, 12, dst) - tidak lagi dipatok
            // "8 Scene" seperti tampilan lama.
            outputSceneBadge.textContent = `${scenes.length} Scene`;
        } else {
            resultsGrid.classList.add('hidden');
            rawFallback.classList.remove('hidden');
            rawOutput.textContent = text;
            outputSceneBadge.textContent = 'Teks Bebas';
        }
    }

    enhancePromptButton.addEventListener('click', () => {
        const basePrompt = promptInput.value.trim();
        const suggestion = 'Sertakan target audiens, pesan utama yang ingin disampaikan, gaya visual (mis. cinematic, ceria, minimalis), dan kalau bisa perkiraan durasi iklan (mis. 15 detik).';
        promptInput.value = basePrompt
            ? `${basePrompt}\n\nTingkatkan dengan: ${suggestion}`
            : suggestion;
        promptInput.focus();
    });

    async function requestGenerateStoryboard() {
        const formData = new FormData();
        formData.append('prompt', promptInput.value.trim());

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch(GENERATE_ENDPOINT, {
            method: 'POST',
            headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
            body: formData,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data.success) {
            throw new Error(data.message || `Generate gagal (HTTP ${response.status}).`);
        }

        return data.storyboard;
    }

    async function handleGenerate() {
        const idea = promptInput.value.trim();

        if (!idea) {
            showStatus('Isi ide iklan terlebih dahulu.', 'error');
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
            const storyboard = await requestGenerateStoryboard();
            showResult(storyboard);
            showStatus('Storyboard berhasil dibuat.', 'info');
        } catch (error) {
            showEmptyState('Generate belum berhasil', error.message || 'Terjadi kesalahan saat membuat storyboard.');
            showStatus(error.message || 'Terjadi kesalahan.', 'error');
        } finally {
            generateButton.disabled = false;
            generateButton.classList.remove('opacity-70', 'cursor-not-allowed');
            generateButtonIcon.textContent = 'auto_awesome';
            generateButtonIcon.classList.remove('animate-spin');
            generateButtonText.textContent = 'Generate Storyboard';
        }
    }

    copyButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(lastRawText);
            copyButton.innerHTML = '<span class="material-symbols-outlined text-[16px]">check</span> Tersalin!';
            setTimeout(() => {
                copyButton.innerHTML = '<span class="material-symbols-outlined text-[16px]">content_copy</span> Copy Semua';
            }, 1800);
        } catch (error) {
            alert('Gagal menyalin ke clipboard.');
        }
    });

    /*
    |----------------------------------------------------------------------
    | BARU: Export storyboard hasil generate menjadi file PDF, supaya bisa
    | langsung diupload ke fitur Carousel tanpa perlu bikin Word/PDF manual
    | sendiri. Memakai jsPDF (dimuat on-demand dari CDN, tidak menambah
    | dependency baru di sisi backend Laravel).
    |----------------------------------------------------------------------
    */

    function loadJsPdf() {
        if (window.jspdf && window.jspdf.jsPDF) {
            return Promise.resolve();
        }

        if (jsPdfLoadPromise) {
            return jsPdfLoadPromise;
        }

        jsPdfLoadPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = JSPDF_CDN_URL;
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Gagal memuat pustaka pembuat PDF. Cek koneksi internet lalu coba lagi.'));
            document.head.appendChild(script);
        });

        return jsPdfLoadPromise;
    }

    async function exportStoryboardPdf() {
        if (!lastRawText) {
            showStatus('Belum ada storyboard untuk diekspor.', 'error');
            return;
        }

        const originalLabel = exportPdfButton.innerHTML;
        exportPdfButton.disabled = true;
        exportPdfButton.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span> Menyiapkan PDF...';

        try {
            await loadJsPdf();

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ unit: 'pt', format: 'a4' });

            const marginX = 48;
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const maxWidth = pageWidth - marginX * 2;
            let y = 64;

            function ensureSpace(lineCount) {
                const needed = lineCount * 15;
                if (y + needed > pageHeight - 56) {
                    doc.addPage();
                    y = 64;
                }
            }

            function writeField(label, value) {
                if (!value) return;

                ensureSpace(1);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.text(`${label}:`, marginX, y);
                y += 15;

                doc.setFont('helvetica', 'normal');
                doc.setFontSize(11);
                const lines = doc.splitTextToSize(value, maxWidth - 14);
                ensureSpace(lines.length);
                doc.text(lines, marginX + 14, y);
                y += lines.length * 15 + 8;
            }

            // Header dokumen
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(18);
            doc.text('Storyboard Video - AI Naraya', marginX, y);
            y += 26;

            doc.setFont('helvetica', 'normal');
            doc.setFontSize(10);
            doc.setTextColor(120);
            doc.text(`Dibuat otomatis oleh AI pada ${new Date().toLocaleString('id-ID')}`, marginX, y);
            doc.setTextColor(20);
            y += 26;

            const scenes = parseScenes(lastRawText);

            if (scenes.length >= 2) {

                // Jumlah scene di PDF ini APA ADANYA sesuai hasil generate
                // (tidak dipotong/ditambah ke angka tertentu seperti 8).
                scenes.forEach((scene) => {
                    ensureSpace(3);

                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(13);
                    doc.text(`Scene ${scene.scene}`, marginX, y);
                    y += 6;

                    doc.setDrawColor(200);
                    doc.line(marginX, y, pageWidth - marginX, y);
                    y += 16;

                    writeField('Visual', scene.visual);
                    writeField('Camera', scene.camera);
                    writeField('Mood', scene.mood);
                    y += 6;
                });

            } else {
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(11);
                const lines = doc.splitTextToSize(lastRawText, maxWidth);
                ensureSpace(lines.length);
                doc.text(lines, marginX, y);
            }

            doc.save(`storyboard-ai-naraya-${Date.now()}.pdf`);

            pdfExportedBanner.classList.remove('hidden');
            showStatus('PDF storyboard berhasil diunduh.', 'info');

        } catch (error) {
            showStatus(error.message || 'Gagal membuat PDF.', 'error');
        } finally {
            exportPdfButton.disabled = false;
            exportPdfButton.innerHTML = originalLabel;
        }
    }

    exportPdfButton.addEventListener('click', exportStoryboardPdf);
    generateButton.addEventListener('click', handleGenerate);
});
</script>
@endsection