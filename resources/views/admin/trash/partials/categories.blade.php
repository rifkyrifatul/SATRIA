@if($categories->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 rounded-full flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </div>
        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tong sampah alur pengajuan kosong</p>
        <p class="text-xs font-medium text-slate-500 mt-1">Tidak ada alur pengajuan yang dihapus saat ini.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-indigo-600 dark:bg-indigo-800">
                    <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider w-16 text-center">No</th>
                    <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Alur Pengajuan</th>
                    <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider">Dihapus Pada</th>
                    <th class="px-6 py-4 text-xs font-extrabold text-white uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-200 dark:divide-slate-700/50">
                @foreach($categories as $index => $category)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-slate-500">{{ $categories->firstItem() + $index }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">{{ $category->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs font-medium">
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $category->deleted_at->format('d M Y') }}</span><br>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $category->deleted_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('super_admin.trash.categories.restore', $category->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Pulihkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    </button>
                                </form>
                                <form action="{{ route('super_admin.trash.categories.force_delete', $category->id) }}" method="POST" onsubmit="confirmDelete(event, 'Yakin hapus permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Hapus Permanen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $categories->links() }}
        </div>
    @endif
@endif
