<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'instansi_name', 'label' => 'Nama Instansi', 'value' => 'PT Ngodingbro Indonesia', 'type' => 'text'],
            ['key' => 'instansi_address', 'label' => 'Alamat Instansi', 'value' => 'Jl. Teknologi No. 99, Jakarta', 'type' => 'textarea'],
            ['key' => 'kepala_instansi', 'label' => 'Nama Kepala Instansi (Penandatangan)', 'value' => 'Budi Santoso, S.Kom', 'type' => 'text'],
            ['key' => 'nip_kepala', 'label' => 'NIP Kepala Instansi', 'value' => '19800101 200501 1 001', 'type' => 'text'],
            ['key' => 'format_nomor_surat', 'label' => 'Format Nomor Surat', 'value' => '[NOMOR]/[KODE]/NGODINGBRO/[BULAN]/[TAHUN]', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
