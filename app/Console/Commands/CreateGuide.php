<?php

namespace App\Console\Commands;

use App\Models\Guide;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateGuide extends Command
{
    protected $signature = 'guide:create {email} {name?}';

    protected $description = 'Buat akun guide baru, atau jadikan user yang sudah ada sebagai guide';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user  = User::where('email', $email)->first();

        if ($user) {
            if ($user->role === 'admin') {
                $this->error('Akun ini admin. Pakai email lain supaya akses admin tidak hilang.');

                return self::FAILURE;
            }

            $user->update(['role' => 'guide']);
            Guide::firstOrCreate(['user_id' => $user->id], ['rating' => 0, 'review_count' => 0]);
            $this->info("{$email} sekarang guide.");

            return self::SUCCESS;
        }

        $name     = $this->argument('name') ?: $this->ask('Nama');
        $password = $this->secret('Password (minimal 8 karakter, tidak tampil saat diketik)');

        if (strlen((string) $password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        $specialty = $this->ask('Spesialisasi, contoh: Waterfall & Trekking (boleh kosong)');
        $languages = $this->ask('Bahasa, contoh: Indonesia, English (boleh kosong)');

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'guide',
        ]);

        Guide::create([
            'user_id'      => $user->id,
            'specialty'    => $specialty,
            'languages'    => $languages,
            'rating'       => 0,
            'review_count' => 0,
        ]);

        $this->info("Guide {$email} berhasil dibuat.");

        return self::SUCCESS;
    }
}