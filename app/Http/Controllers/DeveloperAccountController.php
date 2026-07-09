<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeveloperAccountController extends Controller
{
    public function create()
    {
        return view('developer-create-account');
    }

    public function store(Request $request)
    {
        $request->validate([
            'developer_key' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'developer_key.required' => 'Kode developer wajib diisi.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $developerKey = env('DEV_CREATE_KEY', 'naraya-dev-2026');

        if (! hash_equals($developerKey, (string) $request->developer_key)) {
            return back()
                ->withErrors([
                    'developer_key' => 'Kode developer salah.',
                ])
                ->onlyInput('name', 'email');
        }

        $email = strtolower(trim($request->email));

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => trim($request->name),
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]
        );

        return back()->with([
            'success' => 'Akun berhasil dibuat dan sudah bisa dipakai login.',
            'created_name' => $user->name,
            'created_email' => $user->email,
        ]);
    }
}
