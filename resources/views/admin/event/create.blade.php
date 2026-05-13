@extends('layouts.sidebar')

@section('content')
<main class="p-6 bg-gray-50 flex-1">
    <div class="mb-6">
        <h3 class="text-2xl font-semibold mb-2">Tambahkan Data Event</h3>
    </div>

    <div class="bg-white p-5 rounded-lg shadow max-w-6xl mx-auto">
        <form action="{{ route('event.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama Penyewa / Vendor --}}
            <div class="mb-4 flex items-center">
                <label for="vendor" class="w-1/4 font-medium">Nama Penyewa/Instansi</label>
                <input type="text" name="vendor" id="vendor" value="{{ old('vendor') }}"
                    class="w-3/4 border px-4 py-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                    placeholder="Penyewa" required>
            </div>

            <div class="mb-4 flex items-center">
                <label for="content_id" class="w-1/4 font-medium">Lokasi</label>
                <select name="content_id" id="content_id" required
                    class="w-3/4 border px-4 py-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
                    <option value="">--PILIH LOKASI--</option>
                    @foreach($contents as $ctn)
                        <option value="{{ $ctn->id }}" {{ old('content_id') == $ctn->id ? 'selected' : '' }}>
                            {{ $ctn->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Rentang Tanggal --}}
            <div class="mb-4 flex items-center">
                <label class="w-1/4 font-medium">Tanggal</label>
                <div class="w-3/4 flex gap-2">
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                        class="w-1/2 border px-4 py-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-1/2 border px-4 py-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required>
                </div>
            </div>

            {{-- Nama Event --}}
            <div class="mb-4 flex items-center">
                <label for="name_event" class="w-1/4 font-medium">Nama Event</label>
                <input type="text" name="name_event" id="name_event" value="{{ old('name_event') }}"
                    class="w-3/4 border px-4 py-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                    placeholder="Nama Event" required>
            </div>

            <div class="mb-4 flex items-center">
                <label for="file" class="w-1/4 font-medium">Upload Rundown (PDF)</label>
                <input type="file" name="file" id="file"
                    accept="application/pdf"
                    class="w-3/4 border px-4 py-2 rounded-lg focus:outline-none focus:ring focus:ring-blue-300">
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-6 flex justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium px-6 py-2 rounded-md">
                    Tambah Data
                </button>
                <a href="{{ route('event.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-6 py-2 rounded-md">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</main>

@if(session('error'))
<script>
    Swal.fire({
        title: "Gagal!",
        text: "{{ session('error') }}",
        icon: "error",
        confirmButtonColor: "#d33"
    });
</script>
@endif
@endsection
