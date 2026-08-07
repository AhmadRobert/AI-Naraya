<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateDeveloperKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:generate-key {--clear : Hapus key dari .env untuk mematikan akses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate string acak baru untuk DEVELOPER_ACCESS_KEY dan update file .env otomatis';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            $this->error('File .env tidak ditemukan.');
            return self::FAILURE;
        }

        $envContent = File::get($envPath);

        if ($this->option('clear')) {
            if (preg_match('/^DEVELOPER_ACCESS_KEY=.*/m', $envContent)) {
                $envContent = preg_replace('/^DEVELOPER_ACCESS_KEY=.*/m', '# DEVELOPER_ACCESS_KEY=', $envContent);
                File::put($envPath, $envContent);
            }

            $this->warn('DEVELOPER_ACCESS_KEY berhasil dikosongkan/di-comment. Halaman buat akun sekarang MATI (404).');
            return self::SUCCESS;
        }

        $key = 'nrya_dev_' . Str::random(32);

        if (preg_match('/^#?\s*DEVELOPER_ACCESS_KEY=.*/m', $envContent)) {
            $envContent = preg_replace('/^#?\s*DEVELOPER_ACCESS_KEY=.*/m', "DEVELOPER_ACCESS_KEY='{$key}'", $envContent);
        } else {
            $envContent .= PHP_EOL . "DEVELOPER_ACCESS_KEY='{$key}'" . PHP_EOL;
        }

        File::put($envPath, $envContent);

        $appUrl = rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/');

        $this->info('Developer Access Key baru berhasil digenerate dan disimpan ke .env!');
        $this->line("Key Baru : <comment>{$key}</comment>");
        $this->line("URL Akses: <info>{$appUrl}/buat-akun?key={$key}</info>");

        return self::SUCCESS;
    }
}
