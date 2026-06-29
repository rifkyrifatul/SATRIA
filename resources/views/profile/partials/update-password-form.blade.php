<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-slate-900 dark:text-white">Ubah Kata Sandi</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-500 font-medium text-xs" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-500 font-medium text-xs" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Konfirmasi Kata Sandi</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-500 font-medium text-xs" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60">
            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
                Perbarui Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
