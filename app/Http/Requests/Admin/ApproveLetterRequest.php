<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLetterRequest extends FormRequest
{
    /**
     * Hanya admin yang dapat menyetujui surat.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isAdmin3 = $this->user()?->admin_level === 'admin_3';

        return [
            'letter_number' => [
                $isAdmin3 ? 'required' : 'nullable',
                'string',
                'max:100',
                // Nomor surat harus unik di seluruh tabel letters
                // Kecuali untuk surat itu sendiri (meskipun approve seharusnya sekali)
                'unique:letters,letter_number',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
            'final_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'letter_number.max'      => 'Nomor surat maksimal :max karakter.',
            'letter_number.unique'   => 'Nomor surat ini sudah digunakan oleh surat lain.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'letter_number' => 'Nomor Surat',
            'notes'         => 'Catatan Persetujuan',
            'final_file'    => 'Dokumen Final',
        ];
    }
}
