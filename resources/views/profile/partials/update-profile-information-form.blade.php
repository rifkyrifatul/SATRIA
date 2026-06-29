<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-slate-900 dark:text-white">Informasi Pribadi</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Perbarui nama lengkap dan alamat email Anda untuk menjaga akun tetap mutakhir.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Nama Lengkap</label>
                <input id="name" name="name" type="text" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-rose-500 font-medium text-xs" :messages="$errors->get('name')" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">Alamat Email</label>
                <input id="email" name="email" type="email" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                <x-input-error class="mt-2 text-rose-500 font-medium text-xs" :messages="$errors->get('email')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mt-4">
                <p class="text-sm text-amber-800 font-medium">
                    Alamat email Anda belum diverifikasi.
                    <button form="send-verification" class="font-bold underline text-amber-900 hover:text-amber-700 focus:outline-none">
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-xs font-bold text-emerald-600">
                        Link verifikasi baru telah dikirim ke alamat email Anda.
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60">
            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-600/20">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
