<?php

namespace Database\Factories;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Letter>
 */
class LetterFactory extends Factory
{
    /**
     * Model target factory ini.
     */
    protected $model = Letter::class;

    /**
     * Daftar judul surat realistis dalam konteks perkantoran Indonesia.
     */
    private const LETTER_TITLES = [
        'Permohonan Cuti Tahunan',
        'Surat Pengantar Kunjungan Kerja',
        'Laporan Bulanan Divisi Keuangan',
        'Undangan Rapat Koordinasi',
        'Permohonan Pengadaan Alat Tulis Kantor',
        'Surat Tugas Perjalanan Dinas',
        'Laporan Evaluasi Kinerja Semester I',
        'Permohonan Rekomendasi Vendor',
        'Nota Dinas Revisi Anggaran Q3',
        'Surat Pemberitahuan Jadwal Audit Internal',
        'Permohonan Izin Penggunaan Aula',
        'Laporan Kegiatan Training SDM',
        'Surat Keterangan Aktif Bekerja',
        'Pengajuan Dana Operasional Lapangan',
        'Surat Perjanjian Kerja Sama',
        'Permintaan Data Inventaris Aset',
        'Laporan Insiden dan Tindak Lanjut',
        'Permohonan Penambahan Akses Sistem',
        'Surat Perintah Lembur Karyawan',
        'Pengajuan Proposal Kegiatan HUT Kantor',
    ];

    /**
     * Ekstensi file yang didukung sesuai spesifikasi sistem.
     */
    private const FILE_EXTENSIONS = ['pdf', 'docx', 'doc'];

    /**
     * State default — menghasilkan surat acak dengan status yang bervariasi.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status    = fake()->randomElement(Letter::STATUSES);
        $extension = fake()->randomElement(self::FILE_EXTENSIONS);

        return [
            // Nomor surat: hanya diisi jika status 'approved', selainnya null
            'letter_number' => $status === Letter::STATUS_APPROVED
                ? $this->generateLetterNumber()
                : null,

            'title'          => fake()->randomElement(self::LETTER_TITLES),
            'file_path'      => $this->generateFilePath($extension),
            'file_extension' => $extension,
            'status'         => $status,

            // created_by & updated_by diisi oleh Seeder atau state method
            // Nilai default: ambil staff acak yang sudah ada di DB
            'created_by'     => User::where('role', 'staff')->inRandomOrder()->value('id')
                                ?? User::factory()->staff(),
            'updated_by'     => $status !== Letter::STATUS_PENDING_ADMIN_1
                ? (User::where('role', 'admin')->inRandomOrder()->value('id')
                   ?? User::factory()->admin())
                : null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // State Methods — Untuk membuat surat dengan status tertentu
    // ─────────────────────────────────────────────────────────────────

    /**
     * Buat surat dengan status 'pending'.
     * letter_number pasti null.
     *
     * Contoh: Letter::factory()->pending()->create()
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => Letter::STATUS_PENDING_ADMIN_1,
            'letter_number' => null,
            'updated_by'    => null,
        ]);
    }

    /**
     * Buat surat dengan status 'revision'.
     * letter_number pasti null.
     *
     * Contoh: Letter::factory()->revision()->create()
     */
    public function revision(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => Letter::STATUS_REVISION,
            'letter_number' => null,
        ]);
    }

    /**
     * Buat surat dengan status 'approved'.
     * letter_number selalu diisi dengan format resmi.
     *
     * Contoh: Letter::factory()->approved()->create()
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => Letter::STATUS_APPROVED,
            'letter_number' => $this->generateLetterNumber(),
        ]);
    }

    /**
     * Assign surat ke staff tertentu.
     *
     * Contoh: Letter::factory()->for($staffUser, 'creator')->create()
     * Atau:   Letter::factory()->ownedBy($staffUser)->create()
     */
    public function ownedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Generate nomor surat dengan format resmi perkantoran Indonesia.
     *
     * Format: {nomor_urut}/{kode_unit}/{bulan_romawi}/{tahun}
     * Contoh: 042/TU/SURAT/V/2026
     */
    private function generateLetterNumber(): string
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $unitCodes = ['TU', 'SDM', 'KEU', 'OPS', 'IT', 'HKM'];

        $nomor     = str_pad((string) fake()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
        $unit      = fake()->randomElement($unitCodes);
        $bulan     = $romanMonths[now()->month];
        $tahun     = now()->year;

        return "{$nomor}/{$unit}/SURAT/{$bulan}/{$tahun}";
    }

    /**
     * Generate path file yang realistis di private storage.
     * File asli tidak dibuat — ini hanya path dummy untuk tujuan seeding.
     *
     * Contoh: letters/20260526_143022_AbCdEfGhIjKl.pdf
     */
    private function generateFilePath(string $extension): string
    {
        $timestamp = fake()->dateTimeBetween('-6 months', 'now')->format('Ymd_His');
        $random    = Str::random(12);

        return "letters/{$timestamp}_{$random}.{$extension}";
    }
}
