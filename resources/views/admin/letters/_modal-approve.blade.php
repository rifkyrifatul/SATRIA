{{-- Partial modal approve: dapat diinclude di halaman show maupun dashboard --}}
@props(['modalName', 'letter'])

<div
    x-data="{
        show: false,
        letterId: {{ $letter->id ?? 'null' }},
        letterTitle: '{{ addslashes($letter->title ?? '') }}',
        letterNumber: '',
        notes: '',
        loading: false,
        open(detail) {
            this.letterId     = detail.letterId ?? this.letterId;
            this.letterTitle  = detail.letterTitle ?? this.letterTitle;
            this.letterNumber = '';
            this.notes        = '';
            this.show         = true;
            this.$nextTick(() => this.$refs.letterNumberInput?.focus());
        }
    }"
    @open-modal.window="if($event.detail.name === '{{ $modalName }}') open($event.detail)"
    @keydown.escape.window="show = false"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="show = false"></div>

    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
        @click.stop
    >
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Setujui Surat</h3>
                    <p class="text-xs text-gray-500 truncate max-w-[200px]" x-text="letterTitle"></p>
                </div>
            </div>
            <button @click="show = false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form x-bind:action="`/admin/letters/${letterId}/approve`" method="POST" enctype="multipart/form-data" @submit="loading = true">
            @csrf
            @method('PATCH')
            <div class="px-6 py-5 space-y-4">

                {{-- Nomor Surat (WAJIB) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nomor Surat Resmi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="letter_number"
                            x-ref="letterNumberInput"
                            x-model="letterNumber"
                            required
                            placeholder="Contoh: 001/TU/SURAT/VI/2026"
                            class="w-full pl-10 pr-4 py-3 text-sm border border-gray-200 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                                   placeholder:text-gray-300 font-mono tracking-wide transition"
                        >
                    </div>
                    {{-- Preview nomor surat yang akan ditetapkan --}}
                    <div x-show="letterNumber.trim().length > 0"
                         x-transition
                         class="mt-2 flex items-center gap-2 p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                        </svg>
                        <p class="text-xs text-gray-600">
                            Nomor surat: <strong class="font-mono text-gray-800" x-text="letterNumber"></strong>
                        </p>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">Nomor ini bersifat permanen dan tidak dapat diubah setelah disetujui.</p>
                </div>

                {{-- Catatan (Opsional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Catatan <span class="text-xs font-normal text-gray-400">(opsional)</span>
                    </label>
                    <textarea name="notes" x-model="notes" rows="2"
                              placeholder="Tambahkan catatan jika diperlukan..."
                              class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                                     placeholder:text-gray-300 transition"></textarea>
                </div>

                {{-- Final File (Opsional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Dokumen Final <span class="text-xs font-normal text-gray-400">(opsional)</span>
                    </label>
                    <input type="file" name="final_file" accept=".pdf,.doc,.docx"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-200 rounded-lg bg-gray-50 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1.5">Unggah jika ada perubahan dokumen dari Anda sebelum disetujui (PDF/DOC/DOCX maks 10MB).</p>
                </div>

                {{-- Konfirmasi Banner --}}
                <div class="flex gap-2.5 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-emerald-700">
                        Status surat akan berubah menjadi <strong>Disetujui</strong> secara permanen.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white dark:bg-slate-800 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        :disabled="letterNumber.trim().length === 0 || loading"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-emerald-600
                               rounded-xl hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Setujui Surat'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
