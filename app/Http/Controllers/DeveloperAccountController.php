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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $email = strtolower(trim($request->email));

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => trim($request->name),
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]
        );

        // Auto-rotate DEVELOPER_ACCESS_KEY in .env so current link becomes one-time use
        $newKey = 'nrya_dev_' . \Illuminate\Support\Str::random(32);
        $envPath = base_path('.env');
        if (\Illuminate\Support\Facades\File::exists($envPath)) {
            $envContent = \Illuminate\Support\Facades\File::get($envPath);
            if (preg_match('/^#?\s*DEVELOPER_ACCESS_KEY=.*/m', $envContent)) {
                $envContent = preg_replace('/^#?\s*DEVELOPER_ACCESS_KEY=.*/m', "DEVELOPER_ACCESS_KEY='{$newKey}'", $envContent);
                \Illuminate\Support\Facades\File::put($envPath, $envContent);
            }
        }

        return redirect()->route('login')->with([
            'success' => 'Akun ' . $user->name . ' (' . $user->email . ') berhasil dibuat! Link pembuatan akun telah otomatis hangus (Sekali Pakai).',
        ]);
    }
}
