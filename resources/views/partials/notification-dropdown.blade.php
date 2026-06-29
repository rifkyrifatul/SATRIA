<div x-show="notifOpen" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
     class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1 z-50 overflow-hidden" 
     style="display: none;">
    
    {{-- HEADER --}}
    <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Notifikasi</h3>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.mark_all_read') ?? '#' }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline focus:outline-none transition-colors">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
    
    {{-- BODY --}}
    <div class="max-h-80 overflow-y-auto sidebar-scroll">
        {{-- REAL-TIME NOTIFICATIONS (ALPINE) --}}
        <template x-for="(notif, index) in notifications" :key="index">
            <a href="#" @click.prevent="markAsRead(notif.id, notif.url)" class="block px-4 py-3 border-b border-slate-50 transition-colors bg-indigo-50/40 hover:bg-indigo-50/80">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                         :class="{
                             'bg-emerald-100 text-emerald-600': notif.type === 'success',
                             'bg-amber-100 text-amber-600': notif.type === 'warning',
                             'bg-rose-100 text-rose-600': notif.type === 'danger',
                             'bg-blue-100 text-blue-600': notif.type === 'info' || !notif.type
                         }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="notif.type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            <path x-show="notif.type === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            <path x-show="notif.type === 'danger'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <path x-show="notif.type === 'info' || !notif.type" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="notif.title"></p>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 leading-tight" x-text="notif.message"></p>
                        <p class="text-[10px] text-indigo-500 font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="notif.time"></span>
                        </p>
                    </div>
                    <div class="w-2 h-2 bg-indigo-500 rounded-full mt-1.5 flex-shrink-0 shadow-sm shadow-indigo-500/50"></div>
                </div>
            </a>
        </template>

        <div x-show="notifications.length === 0" class="px-4 py-10 text-center flex flex-col items-center justify-center">
            <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center mb-2">
                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Tidak ada notifikasi baru</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Anda sudah membaca semua pesan.</p>
        </div>
    </div>
    
    {{-- FOOTER --}}
    @if(auth()->user()->notifications->count() > 0)
        <div class="px-4 py-2 border-t border-slate-100 bg-slate-50/50 text-center">
            <a href="#" class="text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-indigo-600 transition-colors">Lihat Semua Notifikasi &rarr;</a>
        </div>
    @endif
</div>
