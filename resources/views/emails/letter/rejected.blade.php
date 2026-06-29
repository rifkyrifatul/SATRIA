<x-mail::message>
# Surat Anda Membutuhkan Revisi

Halo {{ $letter->creator->name ?? 'Staff' }},

Pengajuan surat Anda telah ditinjau, namun membutuhkan revisi sebelum dapat disetujui.

**Judul Surat:** {{ $letter->title }}
**Catatan Revisi:** Anda dapat melihat catatan revisi dari Admin pada halaman detail surat.

<x-mail::button :url="route('staff.letters.show', $letter->id)">
Lihat Detail & Revisi Surat
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
