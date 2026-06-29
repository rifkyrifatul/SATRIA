<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200 p-4 rounded-xl text-sm font-medium shadow-sm" :status="session('status')" />

    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Selamat Datang</h2>
        <p class="text-sm text-slate-500 mt-1">Masuk untuk melanjutkan ke sistem.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="block w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-sm" 
                   placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-medium text-rose-500" />
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input id="password" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                       class="block w-full px-4 py-2.5 pr-12 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-sm" 
                       placeholder="••••••••" />
                <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none transition-colors">
                    <!-- Eye Icon (Show) -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <!-- Eye Slash Icon (Hide) -->
                    <svg x-show="showPassword" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-medium text-rose-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 cursor-pointer" name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">Ingat saya</span>
            </label>
        </div>

        <div class="mt-8 pt-2">
            <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-3 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                Masuk ke Sistem
            </button>
        </div>
    </form>
</x-guest-layout>
