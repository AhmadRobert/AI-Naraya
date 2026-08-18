<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        // Load users with their companies
        $users = User::with('company')->orderBy('created_at', 'desc')->get();
        // Companies for the dropdown in add/edit modals
        $companies = Company::orderBy('name')->get();

        return view('admin.super.users', compact('users', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['super_admin', 'admin_umkm', 'user'])],
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $isSelf = $user->id === auth()->id();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8', // Optional
            'role' => [$isSelf ? 'nullable' : 'required', Rule::in(['super_admin', 'admin_umkm', 'user'])],
            'company_id' => 'nullable|exists:companies,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Prevent self-demotion
        if ($isSelf) {
            unset($validated['role']);
            // Also prevent changing own company if needed, but the user prompt specifically said "edit role diri sendiri".
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'super_admin') {
            return redirect()->back()->with('error', 'Akun dengan tingkat Super Admin tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}
