@extends('layouts.sidebar')

@section('content')

    <main class="p-6 bg-gray-50 flex-1">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">List Jadwal Event</h3>
            <form action="{{ route('event.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by Vendor..." 
                    class="border px-4 py-2 rounded-lg w-64"
                >
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg p-4">
            <div class="mb-4 text-right">
                <a href="{{route('event.create')}}" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg">
                    Tambah Event
                </a>
            </div>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-300 text-white">
                        <th class="p-3">No</th>
                        <th class="p-3"><x-sort-header column="name_event" label="Nama Event" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="start_date" label="Tanggal" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="vendor" label="Nama Penyewa/Instansi" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3">Rundown</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                    <tbody>
                    @forelse ($combined as $item)
                        <tr class="border-b">
                            <td class="p-3 text-center align-middle">{{ $combined->firstItem() + $loop->index }}</td>
                            <td class="p-3 text-center align-middle">{{ $item->name_event }}</td>
                            <td class="p-3 text-center align-middle">
                                {{ \Carbon\Carbon::parse($item->start_date ?? $item->created_at)->format('d/m/Y') }}
                                -
                                {{ \Carbon\Carbon::parse($item->end_date ?? $item->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="p-3 text-left align-middle">
                                {{ $item->vendor ?? '-' }}
                            </td>
                            <td class="p-3 text-center align-middle">
                                @php
                                    $pdfFile = $item->type === 'event' ? $item->file : $item->actv_letter;
                                @endphp

                                @if(!empty($pdfFile))
                                    <a href="{{ asset('storage/' . $pdfFile) }}" target="_blank">
                                        <img src="{{ asset('assets/svg/pdf.svg') }}" alt="PDF" class="w-8 h-8 mx-auto">
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="p-3 text-center align-middle">
                            <div class="flex justify-center space-x-2">
                                <!-- Tombol Edit -->
                                <button 
                                    onclick="window.location='{{ $item->type === 'event' ? route('event.edit', $item->id) : route('submission.edit', $item->id) }}'" 
                                    class="flex items-center justify-center bg-green-500 hover:bg-green-600 w-9 h-9 rounded-lg">
                                    <img src="{{ asset('assets/img/Edit.png') }}" alt="Edit" class="w-5 h-5 object-contain">
                                </button>

                                <!-- Tombol Delete -->
                                <form 
                                    id="delete-form-{{ $item->id }}" 
                                    method="POST" 
                                    action="{{ $item->type === 'event' ? route('event.destroy', $item->id) : route('submission.destroy', $item->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="button" 
                                        data-id="{{ $item->id }}" 
                                        class="flex items-center justify-center bg-red-500 hover:bg-red-600 w-9 h-9 rounded-lg delete-button">
                                        <img src="{{ asset('assets/img/Trash.png') }}" alt="Delete" class="w-5 h-5 object-contain">
                                    </button>
                                </form>
                            </div>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">Data belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $combined->links('vendor.pagination.tailwind') }}
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

        window.addEventListener('pageshow', function (event) {
            if (event.persisted || window.performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
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
    </script>

@endsection