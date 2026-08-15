@extends('layouts.app')

@section('content')
<div class="max-w-[1440px] mx-auto pb-12">

    <div class="mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-label-sm font-bold mb-3">
            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">library_books</span>
            AI Prompt Library
        </div>
        <h1 class="font-headline-lg text-headline-md text-on-surface leading-tight">Prompt Library</h1>
        <p class="mt-2 text-body-md text-on-surface-variant">Koleksi template prompt siap pakai, terorganisir per kategori. Pilih, kustomisasi variabelnya, lalu langsung pakai.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- LEFT: Filter + Add -->
        <aside class="xl:col-span-3">
            <div class="xl:sticky xl:top-24 rounded-3xl border border-outline-variant/70 bg-surface-container-lowest/95 shadow-[0_18px_60px_rgba(0,0,0,0.07)] p-5">
                <h2 class="font-headline-md text-body-md font-bold text-on-surface mb-3">Kategori</h2>

                <div id="categoryList" class="flex flex-col gap-1 mb-6">
                    <button type="button" data-category="" class="category-btn text-left px-3 py-2 rounded-xl bg-primary-container text-on-primary-container font-bold text-label-md transition-all">
                        Semua Kategori
                    </button>
                    @foreach ($categories as $category)
                        <button type="button" data-category="{{ $category }}" class="category-btn text-left px-3 py-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high font-label-md text-label-md transition-all">
                            {{ $category }}
                        </button>
                    @endforeach
                </div>

                <button id="openCreateModal" type="button" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-primary text-on-primary px-4 py-3 font-extrabold shadow-[0_14px_30px_rgba(0,104,116,0.18)] hover:brightness-110 active:scale-[0.98] transition-all">
                    <span class="material-symbols-outlined">add</span>
                    Tambah Prompt Baru
                </button>
            </div>
        </aside>

        <!-- RIGHT: Prompt Grid -->
        <main class="xl:col-span-9">
            <div id="promptGrid" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- diisi lewat JS -->
            </div>

            <div id="emptyState" class="hidden rounded-3xl border-2 border-dashed border-outline-variant/80 bg-surface-container-lowest p-12 text-center">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/50 mb-3 inline-block">search_off</span>
                <p class="text-on-surface-variant">Belum ada prompt di kategori ini.</p>
            </div>
        </main>
    </div>
</div>

<!-- MODAL: Create/Edit -->
<div id="promptModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="modalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-2xl rounded-3xl bg-surface-container-lowest shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 id="modalTitle" class="font-headline-md text-headline-sm text-on-surface">Tambah Prompt Baru</h3>
            <button id="closeModal" type="button" class="w-9 h-9 rounded-xl hover:bg-surface-container-high flex items-center justify-center">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="promptForm" class="space-y-4">
            <input type="hidden" id="promptId" value="">

            <div>
                <label class="block text-label-md font-bold text-on-surface mb-1.5">Kategori</label>
                <select id="formCategory" required class="w-full p-3 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary outline-none">
                    @foreach ($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-label-md font-bold text-on-surface mb-1.5">Judul Prompt</label>
                <input type="text" id="formTitle" required maxlength="150" placeholder="Contoh: Perkenalan Produk Baru" class="w-full p-3 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary outline-none">
            </div>

            <div>
                <label class="block text-label-md font-bold text-on-surface mb-1.5">Template Prompt</label>
                <p class="text-[11px] text-on-surface-variant mb-1.5">Gunakan <code class="bg-surface-container-high px-1 rounded">{nama_variabel}</code> untuk bagian yang bisa diisi ulang, contoh: <code class="bg-surface-container-high px-1 rounded">{nama_produk}</code></p>
                <textarea id="formTemplate" required maxlength="5000" rows="8" class="w-full p-3 rounded-2xl bg-surface-container-low border border-outline-variant focus:border-primary outline-none resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancelModal" class="px-5 py-3 rounded-2xl border border-outline-variant font-bold text-on-surface hover:bg-surface-container transition-all">Batal</button>
                <button type="submit" class="px-5 py-3 rounded-2xl bg-primary text-on-primary font-extrabold hover:brightness-110 transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Use Prompt (isi variabel) -->
<div id="useModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="useModalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-2xl rounded-3xl bg-surface-container-lowest shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 id="useModalTitle" class="font-headline-md text-headline-sm text-on-surface">Pakai Prompt</h3>
            <button id="closeUseModal" type="button" class="w-9 h-9 rounded-xl hover:bg-surface-container-high flex items-center justify-center">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="useVariableFields" class="space-y-3 mb-4"></div>

        <div>
            <label class="block text-label-md font-bold text-on-surface mb-1.5">Hasil</label>
            <textarea id="useResultText" readonly rows="8" class="w-full p-3 rounded-2xl bg-surface-container-low border border-outline-variant outline-none resize-none text-body-sm"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button type="button" id="copyResultButton" class="px-5 py-3 rounded-2xl border border-outline-variant font-bold text-on-surface hover:bg-surface-container transition-all inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                Copy Hasil
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = @json(url('/api/prompts'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const promptGrid = document.getElementById('promptGrid');
    const emptyState = document.getElementById('emptyState');
    const categoryButtons = document.querySelectorAll('.category-btn');

    const promptModal = document.getElementById('promptModal');
    const promptForm = document.getElementById('promptForm');
    const modalTitle = document.getElementById('modalTitle');
    const promptId = document.getElementById('promptId');
    const formCategory = document.getElementById('formCategory');
    const formTitle = document.getElementById('formTitle');
    const formTemplate = document.getElementById('formTemplate');

    const useModal = document.getElementById('useModal');
    const useModalTitle = document.getElementById('useModalTitle');
    const useVariableFields = document.getElementById('useVariableFields');
    const useResultText = document.getElementById('useResultText');
    const copyResultButton = document.getElementById('copyResultButton');

    let allPrompts = [];
    let activeCategory = '';
    let activeUsePromptId = null;

    async function fetchPrompts(category = '') {
        const url = category ? `${API_BASE}?category=${encodeURIComponent(category)}` : API_BASE;
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await response.json();
        return data.prompts || [];
    }

    function renderPrompts(prompts) {
        promptGrid.innerHTML = '';

        if (prompts.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        prompts.forEach((prompt) => {
            const card = document.createElement('article');
            card.className = 'rounded-3xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-[0_10px_30px_rgba(0,0,0,0.05)] flex flex-col';

            const preview = prompt.template.length > 160
                ? prompt.template.slice(0, 160) + '…'
                : prompt.template;

            card.innerHTML = `
                <div class="flex items-start justify-between gap-3 mb-2">
                    <span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary text-[11px] font-bold">${prompt.category}</span>
                    <span class="text-[11px] text-on-surface-variant">${prompt.usage_count || 0}x dipakai</span>
                </div>
                <h3 class="font-headline-sm text-body-lg font-bold text-on-surface mb-2">${prompt.title}</h3>
                <p class="text-body-sm text-on-surface-variant flex-1 whitespace-pre-line">${preview}</p>
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-outline-variant/50">
                    <button data-action="use" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary text-on-primary px-3 py-2 text-label-sm font-bold hover:brightness-110 transition-all">
                        <span class="material-symbols-outlined text-[16px]">bolt</span>
                        Pakai
                    </button>
                    <button data-action="edit" class="w-9 h-9 rounded-xl border border-outline-variant flex items-center justify-center hover:bg-surface-container-high transition-all">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button data-action="delete" class="w-9 h-9 rounded-xl border border-outline-variant text-red-600 flex items-center justify-center hover:bg-red-50 transition-all">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            `;

            card.querySelector('[data-action="use"]').addEventListener('click', () => openUseModal(prompt));
            card.querySelector('[data-action="edit"]').addEventListener('click', () => openEditModal(prompt));
            card.querySelector('[data-action="delete"]').addEventListener('click', () => deletePrompt(prompt));

            promptGrid.appendChild(card);
        });
    }

    async function loadPrompts(category = '') {
        activeCategory = category;
        allPrompts = await fetchPrompts(category);
        renderPrompts(allPrompts);
    }

    categoryButtons.forEach((button) => {
        button.addEventListener('click', () => {
            categoryButtons.forEach((b) => {
                b.classList.remove('bg-primary-container', 'text-on-primary-container');
                b.classList.add('text-on-surface-variant', 'hover:bg-surface-container-high');
            });
            button.classList.add('bg-primary-container', 'text-on-primary-container');
            button.classList.remove('text-on-surface-variant', 'hover:bg-surface-container-high');

            loadPrompts(button.dataset.category);
        });
    });

    // --- Create / Edit modal ---
    function openCreateModal() {
        modalTitle.textContent = 'Tambah Prompt Baru';
        promptId.value = '';
        formCategory.value = formCategory.options[0]?.value || '';
        formTitle.value = '';
        formTemplate.value = '';
        promptModal.classList.remove('hidden');
    }

    function openEditModal(prompt) {
        modalTitle.textContent = 'Edit Prompt';
        promptId.value = prompt.id;
        formCategory.value = prompt.category;
        formTitle.value = prompt.title;
        formTemplate.value = prompt.template;
        promptModal.classList.remove('hidden');
    }

    function closePromptModal() {
        promptModal.classList.add('hidden');
    }

    document.getElementById('openCreateModal').addEventListener('click', openCreateModal);
    document.getElementById('closeModal').addEventListener('click', closePromptModal);
    document.getElementById('cancelModal').addEventListener('click', closePromptModal);
    document.getElementById('modalOverlay').addEventListener('click', closePromptModal);

    promptForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            category: formCategory.value,
            title: formTitle.value.trim(),
            template: formTemplate.value.trim(),
        };

        const isEdit = Boolean(promptId.value);
        const url = isEdit ? `${API_BASE}/${promptId.value}` : API_BASE;

        try {
            const response = await fetch(url, {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Gagal menyimpan prompt.');
            }

            closePromptModal();
            loadPrompts(activeCategory);

        } catch (error) {
            alert(error.message || 'Terjadi kesalahan saat menyimpan prompt.');
        }
    });

    async function deletePrompt(prompt) {
        if (!confirm(`Hapus prompt "${prompt.title}"?`)) return;

        try {
            const response = await fetch(`${API_BASE}/${prompt.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Gagal menghapus prompt.');
            }

            loadPrompts(activeCategory);

        } catch (error) {
            alert(error.message || 'Terjadi kesalahan saat menghapus prompt.');
        }
    }

    // --- Use modal (isi variabel) ---
    function extractVariables(template) {
        const matches = template.match(/\{([a-zA-Z0-9_]+)\}/g) || [];
        return [...new Set(matches.map((m) => m.slice(1, -1)))];
    }

    function renderResult(prompt, values) {
        let result = prompt.template;
        Object.entries(values).forEach(([key, value]) => {
            result = result.split(`{${key}}`).join(value || `{${key}}`);
        });
        useResultText.value = result;
    }

    function openUseModal(prompt) {
        activeUsePromptId = prompt.id;
        useModalTitle.textContent = prompt.title;
        useVariableFields.innerHTML = '';

        const variables = prompt.variables && prompt.variables.length
            ? prompt.variables
            : extractVariables(prompt.template);

        const values = {};

        variables.forEach((variable) => {
            values[variable] = '';

            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <label class="block text-label-sm font-bold text-on-surface mb-1">${variable.replace(/_/g, ' ')}</label>
                <input type="text" data-variable="${variable}" class="w-full p-2.5 rounded-xl bg-surface-container-low border border-outline-variant focus:border-primary outline-none text-body-sm">
            `;

            wrapper.querySelector('input').addEventListener('input', (event) => {
                values[variable] = event.target.value;
                renderResult(prompt, values);
            });

            useVariableFields.appendChild(wrapper);
        });

        renderResult(prompt, values);
        useModal.classList.remove('hidden');
    }

    function closeUseModal() {
        useModal.classList.add('hidden');
    }

    document.getElementById('closeUseModal').addEventListener('click', closeUseModal);
    document.getElementById('useModalOverlay').addEventListener('click', closeUseModal);

    copyResultButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(useResultText.value);

            copyResultButton.innerHTML = '<span class="material-symbols-outlined text-[16px]">check</span> Tersalin!';

            // Catat pemakaian di backend (tanpa mengganggu UX kalau gagal)
            if (activeUsePromptId) {
                fetch(`${API_BASE}/${activeUsePromptId}/use`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ values: {} }),
                }).then(() => loadPrompts(activeCategory));
            }

            setTimeout(() => {
                copyResultButton.innerHTML = '<span class="material-symbols-outlined text-[18px]">content_copy</span> Copy Hasil';
            }, 1800);

        } catch (error) {
            alert('Gagal menyalin ke clipboard.');
        }
    });

    loadPrompts();
});
</script>
@endsection