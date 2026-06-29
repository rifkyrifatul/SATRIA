<?php

namespace Database\Seeders;

use App\Models\Letter;
use App\Models\LetterLog;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk membuat riwayat log yang konsisten dengan status surat.
 *
 * Setiap surat memiliki minimal 1 log 'submit'.
 * Surat 'revision' memiliki tambahan log 'request_revision'.
 * Surat 'approved' memiliki tambahan log 'approve'.
 */
class LetterLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $letters = Letter::with('creator')->get();

        foreach ($letters as $letter) {
            // ── Log 1: 'submit' — selalu ada untuk semua surat ────────────
            LetterLog::create([
                'letter_id' => $letter->id,
                'action'    => LetterLog::ACTION_SUBMIT,
                'notes'     => 'Surat diajukan oleh staff.',
                'user_id'   => $letter->created_by,
                'created_at'=> $letter->created_at,
                'updated_at'=> $letter->created_at,
            ]);

            // ── Log 2: tambahan untuk surat 'revision' ─────────────────────
            if ($letter->isRevision()) {
                LetterLog::create([
                    'letter_id' => $letter->id,
                    'action'    => LetterLog::ACTION_REQUEST_REVISION,
                    'notes'     => fake()->randomElement([
                        'Mohon perbaiki bagian pembukaan surat dan lengkapi lampiran.',
                        'Format penomoran surat belum sesuai standar. Harap diperbaiki.',
                        'Tanda tangan dan cap basah perlu disertakan pada dokumen.',
                        'Isi surat tidak jelas dan perlu diperjelas tujuannya.',
                        'Lampiran pendukung belum dilengkapi. Mohon tambahkan.',
                    ]),
                    'user_id'   => $admin?->id ?? $letter->created_by,
                    'created_at'=> $letter->updated_at,
                    'updated_at'=> $letter->updated_at,
                ]);
            }

            // ── Log 3: tambahan untuk surat 'approved' ─────────────────────
            if ($letter->isApproved()) {
                LetterLog::create([
                    'letter_id' => $letter->id,
                    'action'    => LetterLog::ACTION_APPROVE,
                    'notes'     => "Surat disetujui dengan nomor: {$letter->letter_number}.",
                    'user_id'   => $admin?->id ?? $letter->created_by,
                    'created_at'=> $letter->updated_at,
                    'updated_at'=> $letter->updated_at,
                ]);
            }
        }

        $this->command->info("  ✓ " . LetterLog::count() . " letter logs berhasil dibuat.");
    }
}
