<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestRevisionRequest extends FormRequest
{
    /**
     * Hanya admin yang dapat mengajukan permintaan revisi.
     * Proteksi ganda di samping middleware 'role:admin' pada route.
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
        return [
            'notes' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'notes.required' => 'Catatan revisi wajib diisi.',
            'notes.min'      => 'Catatan revisi minimal :min karakter agar jelas bagi staff.',
            'notes.max'      => 'Catatan revisi maksimal :max karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'notes' => 'Catatan Revisi',
        ];
    }
}
