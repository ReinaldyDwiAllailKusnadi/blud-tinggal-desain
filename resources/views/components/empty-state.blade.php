@props(['message' => 'Data belum tersedia.', 'icon' => 'folder-open'])

<div class="flex flex-col items-center justify-center py-12 px-4">
    <div class="bg-gray-100 dark:bg-slate-700 p-6 rounded-full mb-4">
        @if($icon === 'folder-open')
            <svg class="w-12 h-12 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
        @elseif($icon === 'search')
            <svg class="w-12 h-12 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        @endif
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-slate-100 mb-1">{{ $message }}</h3>
    <p class="text-gray-500 dark:text-slate-400 text-sm text-center max-w-xs">
        @if(request('search'))
            Coba gunakan kata kunci lain untuk pencarian Anda.
        @else
            Silakan tambahkan data baru melalui tombol di atas.
        @endif
    </p>
</div>
