@extends('layouts.info')

@section('title', 'BLUD Pariwisata')
@section('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')

@section('content')
<section class="py-10 px-6">
    <h1 class="bg-primary mx-auto w-max text-center px-8 py-2 rounded-2xl uppercase text-white font-bold text-lg mb-8">
             Formulir Pengajuan Sewa
    </h1>

    <div class="max-w-5xl mx-auto space-y-6">
        
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('user.submission.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Vendor --}}
            <div class="mb-4">
                <label for="namePIC" class="block font-medium mb-1">Nama Pengusul</label>
                <input type="text" name="namePIC" id="namePIC"
                    class="w-full border px-4 py-2 rounded"
                    value="{{ old('namePIC', auth()->user()->name ?? '') }}"
                    placeholder="Masukan Nama Lengkap" required readonly>
            </div>

            <div class="mb-4">
                <label for="no_hp" class="block font-medium mb-1">Nomor Handphone Pengusul</label>
                <input type="text" name="no_hp" id="no_hp" class="w-full border px-4 py-2 rounded" value="{{ old('no_hp') }}" placeholder="Masukan Nomor Handphone Pengusul" required>
            </div>

            <div class="mb-4">
                <label for="vendor" class="block font-medium mb-1">Asal Instansi</label>
                <input type="text" name="vendor" id="vendor" class="w-full border px-4 py-2 rounded" value="{{ old('vendor') }}" placeholder="Masukan Asal Instansi" required>
            </div>

            <div class="mb-4">
                <label for="address" class="block font-medium mb-1">Alamat Instansi</label>
                <input type="text" name="address" id="address" class="w-full border px-4 py-2 rounded" value="{{ old('address') }}" placeholder="Masukan Alamat Instansi" required>
            </div>

            <div class="mb-4">
                <label for="location" class="w-1/4 font-medium">Lokasi</label>
                <select name="location" id="location" required
                    class="w-full border px-4 py-2 rounded">
                    <option value="">--PILIH LOKASI--</option>
                    @foreach($contents as $ctn)
                        <option value="{{ $ctn->name }}" {{ old('location') == $ctn->name ? 'selected' : '' }}>
                            {{ $ctn->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nama Event --}}
            <div class="mb-4">
                <label for="name_event" class="block font-medium mb-1">Nama Kegiatan</label>
                <input type="text" name="name_event" id="name_event" class="w-full border px-4 py-2 rounded" value="{{ old('name_event') }}" placeholder="Masukan nama kegiatan atau deskripsi singkat kegiatan" required>
            </div>


            {{-- Tanggal Mulai dan Selesai --}}
            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label for="start_date" class="block font-medium mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="w-full border px-4 py-2 rounded" value="{{ old('start_date') }}" required>
                </div>
                <div class="w-1/2">
                    <label for="end_date" class="block font-medium mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" class="w-full border px-4 py-2 rounded" value="{{ old('end_date') }}" required>
                </div>
            </div>

            {{-- File Upload --}}
            <div class="mb-4">
                <label for="file" class="block font-medium mb-1">File Proposal</label>
                <input type="file" name="file" id="file" accept=".pdf"
                    class="w-full border px-4 py-2 rounded"
                    onchange="
                        const errorMsg = document.getElementById('file-error');
                        if(this.files[0].size > 5*1024*1024){
                        errorMsg.textContent = 'File maksimal 5 MB';
                        this.value='';
                        } else {
                        errorMsg.textContent = '';
                        }
                    ">
                    <span id="file-error" class="text-red-500 text-sm"></span>
            </div>

            <div class="mb-4">
                <label for="ktp" class="block font-medium mb-1">KTP *</label>
                <input type="file" name="ktp" id="ktp" accept=".pdf,.jpg,.jpeg,.png" class="w-full border px-4 py-2 rounded" required 
                    onchange="
                        const errorMsg = document.getElementById('ktp-error');
                        if(this.files[0].size > 5*1024*1024){
                        errorMsg.textContent = 'File maksimal 5 MB';
                        this.value='';
                        } else {
                        errorMsg.textContent = '';
                        }
                    ">
                    <span id="ktp-error" class="text-red-500 text-sm"></span>
            </div>

            <div class="mb-4">
                <label for="appl_letter" class="block font-medium mb-1">Surat Pengajuan</label>
                <input type="file" name="appl_letter" id="appl_letter" accept=".pdf" class="w-full border px-4 py-2 rounded"
                    onchange="
                        const errorMsg = document.getElementById('appl-error');
                        if(this.files[0].size > 5*1024*1024){
                        errorMsg.textContent = 'File maksimal 5 MB';
                        this.value='';
                        } else {
                        errorMsg.textContent = '';
                        }
                    ">
                    <span id="appl-error" class="text-red-500 text-sm"></span>
            </div>

            <div class="mb-4">
                <label for="actv_letter" class="block font-medium mb-1">Surat Kegiatan / Rundown Kegiatan</label>
                <input type="file" name="actv_letter" id="actv_letter" accept=".pdf" class="w-full border px-4 py-2 rounded" 
                    onchange="
                        const errorMsg = document.getElementById('actv-error');
                        if(this.files[0].size > 5*1024*1024){
                        errorMsg.textContent = 'File maksimal 5 MB';
                        this.value='';
                        } else {
                        errorMsg.textContent = '';
                        }
                    ">
                    <span id="actv-error" class="text-red-500 text-sm"></span>
            </div>
            <p class="text-sm text-red-600 px-4 py-2 rounded-lg mt-2">
                ⚠️ Khusus <strong>KTP</strong> boleh berformat <strong>PDF, JPG, JPEG, atau PNG</strong>.
            </p>
            <p class="text-sm text-red-600 px-4 py-2 rounded-lg mt-0">
                ⚠️ Dokumen lain (Proposal, Surat, Rundown) wajib berformat <strong>PDF</strong>.
            </p>
            <p class="text-sm text-red-600 px-4 py-2 rounded-lg mt-0">
                ⚠️ Ukuran file maksimal adalah <strong>5MB</strong>.
            </p>

            <div class="flex justify-between mt-6">
                @auth
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                        Simpan
                    </button>
                    <a href="{{ route('home') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded">
                        Batal
                    </a>
                @else
                    <div class="text-red-600 font-semibold">
                        Silakan <a href="{{ route('login') }}" class="underline text-blue-600 hover:text-blue-800">login</a> untuk mengisi form ini.
                    </div>
                @endauth
            </div>
        </form>
    </div>
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
