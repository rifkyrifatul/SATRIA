<?php

namespace App\Http\Requests\Staff;

use App\Models\Letter;
use Illuminate\Foundation\Http\FormRequest;

class ResubmitLetterRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan resubmit surat ini.
     *
     * Syarat: surat harus milik staff yang login DAN berstatus 'revision'.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\Letter|null $letter */
        $letter = $this->route('letter');

        return $letter !== null
            && $letter->created_by === $this->user()->id
            && $letter->isRevision();
    }

    /**
     * Aturan validasi untuk resubmit surat revisi.
     *
     * File bersifat opsional: staff boleh hanya update judul,
     * atau mengunggah file baru sekaligus.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],
            'file' => [
                'nullable',   // File tidak wajib saat resubmit (judul bisa diubah saja)
                'file',
                'mimes:pdf,docx,doc',
                'extensions:pdf,docx,doc',
                'max:10240',  // 10MB
            ],
        ];
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul surat wajib diisi.',
            'title.min'      => 'Judul surat minimal :min karakter.',
            'title.max'      => 'Judul surat maksimal :max karakter.',

            'file.file'       => 'Upload harus berupa file yang valid.',
            'file.mimes'      => 'File harus berformat PDF atau DOCX/DOC.',
            'file.extensions' => 'Ekstensi file harus .pdf, .docx, atau .doc.',
            'file.max'        => 'Ukuran file tidak boleh melebihi 10MB.',
        ];
    }

    /**
     * Pesan yang dikembalikan saat otorisasi gagal (bukan milik user / bukan revision).
     */
    public function failedAuthorization(): never
    {
        abort(403, 'Anda tidak berhak melakukan resubmit pada surat ini, atau surat tidak dalam status Revisi.');
    }
}
