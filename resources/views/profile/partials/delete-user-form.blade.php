<section class="space-y-6">
    <header class="mb-6">
        <h2 class="text-xl font-black text-red-600 dark:text-red-400">Hapus Akun</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Setelah akun Anda dihapus, semua data dan sumber daya yang terkait akan dihapus secara permanen. Pastikan Anda telah mengunduh data apa pun yang ingin Anda simpan.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm shadow-red-600/20"
    >
        Hapus Akun Secara Permanen
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-white dark:bg-slate-800 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-slate-900 dark:text-white">
                Apakah Anda yakin ingin menghapus akun ini?
            </h2>

            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Semua data Anda akan terhapus permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini.
            </p>

            <div class="mt-6">
                <label for="password" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2 sr-only">Kata Sandi</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full md:w-3/4 px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                    placeholder="Masukkan Kata Sandi Anda"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-500 font-medium text-xs" />
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                    Batal
                </button>

                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm shadow-red-600/20">
                    Ya, Hapus Akun Saya
                </button>
            </div>
        </form>
    </x-modal>
</section>
