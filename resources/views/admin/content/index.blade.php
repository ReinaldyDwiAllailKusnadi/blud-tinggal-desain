@extends('layouts.sidebar')

@section('content')

    <main class="p-6 bg-gray-50 dark:bg-slate-900 flex-1 transition-colors duration-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-slate-50">List Tempat Wisata</h3>
            <form action="{{ route('content.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by name..." 
                    class="border px-4 py-2 rounded-lg w-64 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50 dark:placeholder-slate-400"
                >
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-md rounded-lg p-4 transition-colors duration-200">
            <div class="mb-4 text-right">
                <a href="{{route('content.create')}}" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg">
                    Tambah Data Wisata
                </a>
            </div>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-300 dark:bg-slate-700 text-white dark:text-slate-50">
                        <th class="p-3">No</th>
                        <th class="p-3"><x-sort-header column="name" label="Nama Tempat" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="price_weekday" label="Harga Tiket" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3">Jam Operasional</th>
                        <th class="p-3">Kapasitas & Tipe</th>
                        <th class="p-3"><x-sort-header column="location" label="Lokasi" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3">Foto</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contents as $content)
                        <tr class="border-b dark:border-slate-700 text-gray-800 dark:text-slate-300">
                            <td class="p-3 text-center align-middle">{{ $contents->firstItem() + $loop->index }}</td>
                            <td class="p-3 text-center align-middle">{{ $content->name }}</td>
                            <td class="p-3 text-center align-middle">
                                Rp{{ $content->price_weekday ? number_format((int) $content->price_weekday, 0, ',', '.') : '-' }}
                            </td>
                             <td class="p-3 text-center align-middle">
                                {{ \Carbon\Carbon::parse($content->open_time)->format('h:i') }} -
                                {{ \Carbon\Carbon::parse($content->close_time)->format('h:i') }}
                            </td>
                            <td class="p-3 text-center align-middle">
                                <span class="block font-semibold">{{ $content->capacity ?? '-' }} Peserta</span>
                                <span class="text-xs text-gray-500 uppercase">{{ $content->venue_type ?? 'Umum' }}</span>
                                <div class="mt-1 flex justify-center gap-1">
                                    @if($content->is_indoor)
                                        <span class="px-1 bg-blue-100 text-blue-700 text-[10px] rounded">Indoor</span>
                                    @endif
                                    @if($content->is_outdoor)
                                        <span class="px-1 bg-green-100 text-green-700 text-[10px] rounded">Outdoor</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3 text-center align-middle">{{ $content->location }}</td>
                            <td class="p-3 text-center align-middle">
                                @if ($content->image)
                                    <a href="{{ asset($content->image) }}" target="_blank">
                                        <img src="{{ asset('assets/img/Picture.png') }}" alt="image" class="w-8 h-8 mx-auto">
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="p-3 align-middle text-center">
                                <button type="button" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
                                    onclick="openModal({{ $content->id }})">
                                    Preview
                                </button>

                                <!-- Modal -->
                                <div id="modal-{{ $content->id }}" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 relative transition-colors duration-200">
                                        <button onclick="closeModal({{ $content->id }})" class="absolute top-2 right-2 text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 text-5xl">&times;</button>
                                        
                                        @if ($content->image)
                                            <img src="{{ asset($content->image) }}" alt="image" class="w-60 h-40 object-cover mx-auto mb-4 rounded">
                                        @endif

                                        <h2 class="text-xl font-semibold text-center mb-2 text-gray-800 dark:text-slate-50">{{ $content->name }}</h2>

                                        <p class="text-gray-600 dark:text-slate-300 text-sm text-center mb-4">
                                            <strong>Harga Tiket Weekday : </strong>Rp{{ $content->price_weekday ? number_format((int) $content->price_weekday, 0, ',', '.') : '-' }}<br>
                                            <strong>Harga Tiket Weekend : </strong>Rp{{ $content->price_weekend ? number_format((int) $content->price_weekend, 0, ',', '.') : '-' }}<br>
                                            <strong>Jam Operasional : </strong> {{ \Carbon\Carbon::parse($content->open_time)->format('h:i') }} - {{\Carbon\Carbon::parse($content->close_time)->format('h:i') }}<br>
                                            <strong>Lokasi : </strong> {{ $content->location }}<br>
                                            <div class="mt-2 pt-2 border-t dark:border-slate-700">
                                                <strong>Kapasitas Maksimal : </strong> {{ $content->capacity ?? '-' }} Peserta<br>
                                                <strong>Tipe Lokasi : </strong> {{ $content->venue_type ?? '-' }}<br>
                                                <strong>Karakteristik : </strong> 
                                                @if($content->is_indoor) <span class="text-blue-500">Indoor</span> @endif
                                                @if($content->is_indoor && $content->is_outdoor) & @endif
                                                @if($content->is_outdoor) <span class="text-green-500">Outdoor</span> @endif
                                            </div>
                                        </p>

                                        <div class="text-gray-800 dark:text-slate-300 text-justify leading-relaxed border-t dark:border-slate-700 pt-4">
                                            {!! nl2br(e($content->description)) !!}
                                        </div>
                                    </div>
                                </div>

                            </td>
                            <td class="p-3 text-center align-middle">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="window.location='{{ route('content.edit', $content->id) }}'" class="flex items-center justify-center bg-green-500 hover:bg-green-600 w-9 h-9 rounded-lg">
                                            <img src="{{ asset('assets/img/Edit.png') }}" alt="Edit" class="w-5 h-5 object-contain">
                                    </button>
                                    <form id="delete-form-{{ $content->id }}" method="POST" action="{{ route('content.destroy', $content->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" data-id="{{ $content->id }}" class="flex items-center justify-center bg-red-500 hover:bg-red-600 w-9 h-9 rounded-lg delete-button">
                                            <img src="{{ asset('assets/img/Trash.png') }}" alt="Delete" class="w-5 h-5 object-contain">
                                        </button>
                                    </form>
                                </div>
                             </td>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4 text-gray-500">Data belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $contents->links('vendor.pagination.tailwind') }}
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tombol Hapus
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const adminId = this.getAttribute('data-id');

                    Swal.fire({
                        title: "Yakin ingin dihapus?",
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${adminId}`).submit();
                        }
                    });
                });
            });
        });

        // Jalankan flash hanya jika halaman bukan dari bfcache
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || window.performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
                // Jangan jalankan apa-apa jika halaman dari cache
                return;
            }

            @if(session('error'))
            Swal.fire({
                title: "Gagal!",
                text: @json(session('error')),
                icon: "error",
                confirmButtonColor: "#d33"
            });
            @endif

            @if(session('success'))
            Swal.fire({
                title: "Berhasil!",
                text: @json(session('success')),
                icon: "success",
                confirmButtonColor: "#3085d6"
            });
            @endif
        });

        function openModal(id) {
            document.getElementById(`modal-${id}`).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(`modal-${id}`).classList.add('hidden');
        }

        function closeModal(id) {
            document.getElementById(`modal-${id}`).classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                modal.addEventListener('click', function (e) {
                    // Jika klik di luar konten modal (div dengan class bg-white)
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            });
        });
    </script>

@endsection