@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 relative">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Manajemen User</h1>
            <p class="text-gray-600 dark:text-gray-300">Kelola seluruh akun pengguna di platform AI Naraya.</p>
        </div>
        <button onclick="document.getElementById('modal-add-user').classList.remove('hidden')" class="focus-ring inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-soft transition hover:-translate-y-0.5 hover:brightness-105 active:translate-y-0 active:scale-[0.98]">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            Tambah User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama & Email</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Perusahaan (UMKM)</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tgl Terdaftar</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $u->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $u->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($u->role === 'super_admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">Super Admin</span>
                                @elseif($u->role === 'admin_umkm')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Admin UMKM</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">User</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">
                                {{ $u->company ? $u->company->name : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $u->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}', '{{ $u->company_id }}', {{ $u->id === auth()->id() ? 'true' : 'false' }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit User">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    @if($u->id !== auth()->id() && $u->role !== 'super_admin')
                                        <button onclick="openDeleteModal({{ $u->id }}, '{{ addslashes($u->name) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus User">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Belum ada user terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div id="modal-add-user" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Tambah User Baru</h3>
            <form action="{{ route('admin.super.users.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" required minlength="8" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                            <select name="role" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                <option value="user">User Biasa</option>
                                <option value="admin_umkm">Admin UMKM</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perusahaan (UMKM)</label>
                            <select name="company_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-add-user').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white hover:brightness-105 shadow-soft transition font-medium">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="modal-edit-user" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Edit User</h3>
            <form id="form-edit-user" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                        <input type="text" id="edit-name" name="name" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" id="edit-email" name="email" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Baru (Opsional)</label>
                        <input type="password" name="password" minlength="8" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                            <select id="edit-role" name="role" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                <option value="user">User Biasa</option>
                                <option value="admin_umkm">Admin UMKM</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perusahaan (UMKM)</label>
                            <select id="edit-company" name="company_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white hover:brightness-105 shadow-soft transition font-medium">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="modal-delete-user" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full max-w-sm p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-600 mb-4">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Pengguna?</h3>
            <p class="text-sm text-gray-500 mb-6">Anda yakin ingin menghapus akun <strong id="delete-user-name"></strong> secara permanen? Tindakan ini tidak dapat dibatalkan.</p>
            <form id="form-delete-user" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('modal-delete-user').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 transition font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 shadow-soft transition font-medium">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, email, role, company_id, isSelf = false) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-email').value = email;
        
        let roleInput = document.getElementById('edit-role');
        let companyInput = document.getElementById('edit-company');
        
        roleInput.value = role;
        companyInput.value = company_id || '';
        
        if (isSelf) {
            roleInput.disabled = true;
            roleInput.title = "Anda tidak dapat mengubah role akun Anda sendiri";
            roleInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            
            // To ensure the value is still submitted if we use disabled, we shouldn't fully disable it or we need to add a hidden input.
            // Actually, an easier way is to just let the backend handle the protection, and we just visually show it's restricted.
            // Or we use pointer-events-none.
            roleInput.style.pointerEvents = 'none';
        } else {
            roleInput.disabled = false;
            roleInput.title = "";
            roleInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            roleInput.style.pointerEvents = 'auto';
        }

        document.getElementById('form-edit-user').action = '/super-admin/users/' + id;
        document.getElementById('modal-edit-user').classList.remove('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete-user-name').textContent = name;
        document.getElementById('form-delete-user').action = '/super-admin/users/' + id;
        document.getElementById('modal-delete-user').classList.remove('hidden');
    }
</script>
@endsection
