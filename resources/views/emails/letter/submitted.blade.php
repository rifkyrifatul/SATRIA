<x-mail::message>
# Pengajuan Surat Baru

Halo Admin,

Terdapat pengajuan surat baru yang membutuhkan tinjauan Anda.

**Judul Surat:** {{ $letter->title }}
**Kategori:** {{ $letter->category->name ?? '-' }}
**Diajukan Oleh:** {{ $letter->creator->name ?? 'Staff' }}

<x-mail::button :url="route('admin.letters.show', $letter->id)">
Lihat Detail Surat
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
