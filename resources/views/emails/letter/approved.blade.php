<x-mail::message>
# Surat Anda Telah Disetujui!

Halo {{ $letter->creator->name ?? 'Staff' }},

Kabar baik! Pengajuan surat Anda telah disetujui oleh Admin.

**Judul Surat:** {{ $letter->title }}
**Nomor Surat:** {{ $letter->letter_number ?? '-' }}

<x-mail::button :url="route('staff.letters.show', $letter->id)">
Lihat Detail Surat
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
