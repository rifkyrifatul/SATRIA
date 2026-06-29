{{-- Partial modal: dapat diinclude di halaman show maupun dashboard --}}
@props(['modalName', 'letter'])

<div
    x-data="{
        show: false,
        letterId: {{ $letter->id ?? 'null' }},
        letterTitle: '{{ addslashes($letter->title ?? '') }}',
        notes: '',
        loading: false,
        open(detail) {
            this.letterId    = detail.letterId ?? this.letterId;
            this.letterTitle = detail.letterTitle ?? this.letterTitle;
            this.notes       = '';
            this.show        = true;
            this.$nextTick(() => this.$refs.notesInput?.focus());
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
                <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Minta Revisi</h3>
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

        <form x-bind:action="`/admin/letters/${letterId}/revision`" method="POST" @submit="loading = true">
            @csrf
            @method('PATCH')
            <div class="px-6 py-5 space-y-4">
                <div class="flex gap-2.5 p-3 bg-amber-50 rounded-xl border border-amber-100">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-amber-700">Catatan revisi akan dikirim ke staff sebagai panduan perbaikan.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Catatan Revisi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="notes" x-ref="notesInput" x-model="notes" rows="4" required minlength="10"
                              placeholder="Jelaskan secara spesifik bagian mana yang perlu diperbaiki..."
                              class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl resize-none
                                     focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent
                                     placeholder:text-gray-300 transition"></textarea>
                    <p class="text-xs text-gray-400 mt-1" x-text="`${notes.length} / 1000 karakter (minimal 10)`"></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-white dark:bg-slate-800 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" :disabled="notes.length < 10 || loading"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-amber-500
                               rounded-xl hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Kirim Permintaan Revisi'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
