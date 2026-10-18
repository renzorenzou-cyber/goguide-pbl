<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {name?}';

    protected $description = 'Buat akun admin baru, atau jadikan user yang sudah ada sebagai admin';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user  = User::where('email', $email)->first();

        // User sudah ada: cukup naikkan jadi admin
        if ($user) {
            $user->update(['role' => 'admin']);
            $this->info("{$email} sekarang admin.");

            return self::SUCCESS;
        }

        $name     = $this->argument('name') ?: $this->ask('Nama');
        $password = $this->secret('Password (minimal 8 karakter, tidak tampil saat diketik)');

        if (strlen((string) $password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
        ]);

        $this->info("Admin {$email} berhasil dibuat.");

        return self::SUCCESS;
    }
}