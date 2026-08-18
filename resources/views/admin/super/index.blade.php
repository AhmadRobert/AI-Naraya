@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 relative">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Super Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-300">Overview of AI usage and costs across all companies.</p>
        </div>
        <button onclick="document.getElementById('modal-add-umkm').classList.remove('hidden')" class="focus-ring inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-soft transition hover:-translate-y-0.5 hover:brightness-105 active:translate-y-0 active:scale-[0.98]">
            <span class="material-symbols-outlined text-[20px]">add_business</span>
            Tambah UMKM
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Raw Cost</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($totals->total_raw_cost ?? 0, 2, ',', '.') }}
                </h3>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Billed Cost</p>
                <h3 class="text-3xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($totals->total_billed_cost ?? 0, 2, ',', '.') }}
                </h3>
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg text-green-600 dark:text-green-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
    </div>

    <!-- Company Usage Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Usage by Company</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company Name</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Markup %</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Total Requests</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Raw Cost</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Billed Cost</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Profit</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($companies as $company)
                        @php
                            $log = $companyUsage->firstWhere('company_id', $company->id)?->aiUsageLogs->first();
                            $rawCost = $log->total_raw_cost ?? 0;
                            $billedCost = $log->total_billed_cost ?? 0;
                            $profit = $billedCost - $rawCost;
                            $requests = $log->total_requests ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$company->is_active ? 'opacity-75 bg-gray-50/50 dark:bg-gray-800/50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $company->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($company->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Diblokir</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $company->markup_percentage }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium">
                                    {{ $requests }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                Rp {{ number_format($rawCost, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400 text-right">
                                Rp {{ number_format($billedCost, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400 text-right">
                                Rp {{ number_format($profit, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ $company->id }}, '{{ $company->name }}', {{ $company->markup_percentage }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Markup">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <form action="{{ route('admin.super.companies.toggle', $company->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 {{ $company->is_active ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition" title="{{ $company->is_active ? 'Blokir UMKM' : 'Aktifkan UMKM' }}">
                                            <span class="material-symbols-outlined text-[20px]">{{ $company->is_active ? 'block' : 'check_circle' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                No usage data available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah UMKM -->
    <div id="modal-add-umkm" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-md p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Tambah UMKM Baru</h3>
            <form action="{{ route('admin.super.companies.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Markup Persentase (%)</label>
                        <input type="number" name="markup_percentage" value="50" min="0" step="0.01" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-add-umkm').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white hover:brightness-105 shadow-soft transition font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Markup -->
    <div id="modal-edit-markup" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-md p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Edit Markup UMKM</h3>
            <p id="edit-modal-company-name" class="text-sm text-gray-500 mb-4"></p>
            <form id="form-edit-markup" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Markup Persentase (%)</label>
                        <input type="number" id="edit-markup-input" name="markup_percentage" min="0" step="0.01" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-markup').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white hover:brightness-105 shadow-soft transition font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, currentMarkup) {
        document.getElementById('edit-modal-company-name').textContent = 'Perusahaan: ' + name;
        document.getElementById('edit-markup-input').value = currentMarkup;
        document.getElementById('form-edit-markup').action = '/super-admin/companies/' + id + '/markup';
        document.getElementById('modal-edit-markup').classList.remove('hidden');
    }
</script>
@endsection
