@extends('layouts.sidebar')

@section('content')
<div class="flex-1 flex flex-col">
    <main class="p-6 bg-gray-50 flex-1">
       <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Daftar Pengguna Admin</h3>
            <form action="{{ route('account.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by username..." 
                    class="border px-4 py-2 rounded-lg w-64"
                >
            </form>
        </div>


        <div class="bg-white shadow-md rounded-lg p-4">
            <div class="mb-4 text-right">
                <a href="{{route('account.create')}}" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg">
                    Tambahkan Admin
                </a>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-300 text-white">
                        <th class="p-3">No</th>
                        <th class="p-3"><x-sort-header column="id" label="AdminID" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="username" label="Username Admin" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="created_at" label="Activate Date" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3">Identitas Pengguna</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr class="border-b">
                            <td class="p-3 text-center align-middle">{{ $admins->firstItem() + $loop->index }}</td>
                            <td class="p-3 text-center align-middle">{{ $admin->id }}</td>
                            <td class="p-3 text-left align-middle">{{ $admin->username }}</td>
                            <td class="p-3 text-center align-middle">
                                {{ \Carbon\Carbon::parse($admin->activated_at)->format('d/m/Y') }}
                            </td>
                            <td class="p-3 text-left align-middle">
                                <span class="font-semibold">Nama:</span> {{ $admin->name }} <br>
                                <span class="font-semibold">Email:</span> {{ $admin->email }} <br>
                                <span class="font-semibold">No. Hp:</span> {{ $admin->phone }}
                            </td>
                            <td class="p-3 text-center align-middle">
                                <div class="flex justify-center space-x-2">
                                    <button 
                                        onclick="window.location='{{ route('account.edit', $admin->id) }}'" 
                                        class="flex items-center justify-center bg-green-500 hover:bg-green-600 w-9 h-9 rounded-lg"
                                        >
                                        <img src="{{ asset('assets/img/Edit.png') }}" alt="Edit" class="w-5 h-5 object-contain">
                                    </button>

                                    <form id="delete-form-{{ $admin->id }}" method="POST" action="{{ route('account.destroy', $admin->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="button" 
                                            data-id="{{ $admin->id }}" 
                                            class="flex items-center justify-center bg-red-500 hover:bg-red-600 w-9 h-9 rounded-lg delete-button"
                                        >
                                            <img src="{{ asset('assets/img/Trash.png') }}" alt="Delete" class="w-5 h-5 object-contain">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <x-empty-state 
                                    message="{{ request('search') ? 'Tidak ada admin dengan username \"' . request('search') . '\"' : 'Belum ada data pengguna admin.' }}" 
                                    icon="{{ request('search') ? 'search' : 'folder-open' }}" 
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $admins->links('vendor.pagination.tailwind') }}
        </div>
    </main>
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
</script>
@endsection
