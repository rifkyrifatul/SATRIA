<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak membuat request ini.
     *
     * Sudah diproteksi oleh middleware 'role:staff' di route,
     * jadi cukup kembalikan true di sini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk upload surat baru.
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
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'file' => [
                'required',
                'file',
                // Validasi MIME type yang sesungguhnya (bukan hanya ekstensi)
                'mimes:pdf,docx,doc',
                // Validasi ekstensi tambahan sebagai lapisan kedua keamanan
                'extensions:pdf,docx,doc',
                // Maksimal 10MB (dalam kilobytes: 10 * 1024)
                'max:10240',
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

            'category_id.required' => 'Kategori surat wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',

            'file.required'   => 'File surat wajib diunggah.',
            'file.file'       => 'Upload harus berupa file yang valid.',
            'file.mimes'      => 'File harus berformat PDF atau DOCX/DOC.',
            'file.extensions' => 'Ekstensi file harus .pdf, .docx, atau .doc.',
            'file.max'        => 'Ukuran file tidak boleh melebihi 10MB.',
        ];
    }

    /**
     * Label atribut yang lebih ramah untuk pesan error.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title'       => 'Judul Surat',
            'category_id' => 'Kategori Surat',
            'file'        => 'File Surat',
        ];
    }
}
