<?php

namespace Database\Seeders;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan eksekusi:
     * 1. Buat akun Admin & Staff tetap (untuk login testing)
     * 2. Buat beberapa akun Staff acak (data pendukung)
     * 3. Buat surat-surat dummy dengan variasi status
     * 4. Jalankan LetterLogSeeder untuk membuat riwayat log
     */
    public function run(): void
    {
        $this->command->info('🌱 Memulai proses seeding...');
        $this->command->newLine();

        // ══════════════════════════════════════════════════════════════
        // STEP 1 — Akun Super Admin
        // ══════════════════════════════════════════════════════════════
        $this->command->info('👤 Membuat akun Super Admin...');

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@kantor.com'],
            [
                'name'                 => 'Super Administrator',
                'password'             => Hash::make('password'),
                'role'                 => 'super_admin',
                'email_verified_at'    => now(),
                'must_change_password' => false, // Akun awal sistem, tidak perlu paksa ganti password
            ]
        );

        $this->command->line("  ✓ Super Admin: {$superAdmin->email}");

        // ══════════════════════════════════════════════════════════════
        // STEP 2 — Divisi Staff
        // ══════════════════════════════════════════════════════════════
        $this->command->info('🏢 Membuat Divisi Staff...');
        $divisions = [
            'Kesiswaan',
            'Pendidikan',
            'Administrasi',
            'CF2LT',
            'CLC',
            'LCBC',
            'HOCC',
            'EXCELSA',
        ];

        foreach ($divisions as $divName) {
            \App\Models\StaffDivision::firstOrCreate(['name' => $divName]);
        }
        $this->command->line("  ✓ " . count($divisions) . " Divisi berhasil ditambahkan.");

        // ══════════════════════════════════════════════════════════════
        // Ringkasan akhir
        // ══════════════════════════════════════════════════════════════
        $this->command->newLine();
        $this->command->info('✅ Seeding selesai! Ringkasan data:');
        $this->command->table(
            ['Tipe Data', 'Jumlah'],
            [
                ['Users (Total)',   User::count()],
                ['Users Super Admin', User::where('role', 'super_admin')->count()],
                ['Users Admin',     User::where('role', 'admin')->count()],
                ['Users Staff',     User::where('role', 'staff')->count()],
            ]
        );

        $this->command->newLine();
        $this->command->line('🔑 <fg=yellow>Akun untuk login:</>');
        $this->command->line('   Super Admin → <fg=green>superadmin@kantor.com</> / <fg=green>password</>');
    }
}
