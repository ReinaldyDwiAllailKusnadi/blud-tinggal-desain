@extends('layouts.sidebar')

@section('content')
<div class="flex-1 flex flex-col" x-data="{ showModal: false, user: {} }">
    <main class="p-6 bg-gray-50 flex-1">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Daftar Pengguna</h3>
            <form action="{{ route('user.index') }}" method="GET">
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
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-300 text-white">
                        <th class="p-3">No</th>
                        <th class="p-3"><x-sort-header column="id" label="UserID" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="username" label="Username Account" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3"><x-sort-header column="activated_at" label="Activate Date" :sortBy="$sortBy" :sortDir="$sortDir" /></th>
                        <th class="p-3">Identitas Pengguna</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b">
                            <td class="p-3 text-center align-middle">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="p-3 text-center align-middle">{{ $user->id }}</td>
                            <td class="p-3 text-left align-middle">{{ $user->username }}</td>
                            <td class="p-3 text-center align-middle">
                                {{ \Carbon\Carbon::parse($user->activated_at)->format('d/m/Y') }}
                            </td>
                            <td class="p-3 text-left align-middle">
                                <span class="font-semibold">Nama:</span> {{ $user->name }} <br>
                                <span class="font-semibold">Email:</span> {{ $user->email }} <br>
                                <span class="font-semibold">No. Hp:</span> {{ $user->phone }}
                            </td>
                            <td class="p-3 text-center align-middle">
                                <div class="flex justify-center space-x-2">
                                    <!-- Tombol Edit -->
                                    <button
                                        type="button"
                                        @click.prevent="
                                            showModal = true;
                                            user = {
                                                id: '{{ $user->id }}',
                                                username: '{{ $user->username }}',
                                                name: '{{ $user->name }}',
                                                email: '{{ $user->email }}',
                                                phone: '{{ $user->phone }}'
                                            };
                                        "
                                        class="flex items-center justify-center bg-green-500 hover:bg-green-600 w-9 h-9 rounded-lg">
                                        <img src="{{ asset('assets/img/Edit.png') }}" alt="Edit" class="w-5 h-5 object-contain">
                                    </button>

                                    <!-- Tombol Delete -->
                                    <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('user.destroy', $user->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" data-id="{{ $user->id }}" class="delete-button bg-red-500 hover:bg-red-600 p-2 rounded-lg">
                                            <img src="{{ asset('assets/img/Trash.png') }}" alt="Delete" class="w-5 h-5">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">Data pengguna belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>

        <!-- Modal Edit User -->
        <div x-show="showModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showModal = false" class="bg-white p-6 rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-semibold mb-4">Edit Data Pengguna</h2>
                <form :action="'{{ url('admin/user') }}/' + user.id" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">Username Account</label>
                        <input type="text" name="username" x-model="user.username" class="w-full border p-2 rounded bg-gray-100" required>
                        <p class="text-xs text-gray-500 mt-1">* Digunakan untuk login aplikasi</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">Nama Lengkap</label>
                        <input type="text" name="name" x-model="user.name" class="w-full border p-2 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">Email</label>
                        <input type="email" name="email" x-model="user.email" class="w-full border p-2 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">No Telepon / WhatsApp</label>
                        <input type="text" name="phone" x-model="user.phone" class="w-full border p-2 rounded">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="w-full border p-2 rounded" placeholder="Kosongkan jika tidak ingin diubah">
                        <p class="text-xs text-gray-500 mt-1">* Minimal 6 karakter</p>
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" @click="showModal = false" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
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
