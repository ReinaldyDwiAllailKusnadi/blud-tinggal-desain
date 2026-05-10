@extends('layouts.sidebar')

@section('content')
<main class="p-6 bg-gray-50 flex-1 overflow-y-auto">
    <div class="mb-6">
        <h3 class="text-2xl font-semibold mb-2">Tambah Tempat Wisata</h3>
    </div>

    <div class="bg-white p-5 rounded-lg shadow max-w-6xl mx-auto">
        <form action="{{ route('content.store') }}" method="POST" enctype="multipart/form-data" id="form-content">
            @csrf

            {{-- Nama Tempat --}}
            <div class="mb-4 flex items-center">
                <label for="name" class="w-1/4 font-medium">Nama Tempat</label>
                <input type="text" name="name" id="name" class="border px-4 py-2 rounded-lg w-3/4" value="{{ old('name') }}" required>
            </div>

            <div class="mb-4 flex items-center">
                <label for="medsos" class="w-1/4 font-medium">Sosial Media</label>
                <div class="flex gap-4 w-3/4">
                    <div class="flex flex-col w-1/2">
                        <label for="instagram" class="text-sm font-medium mb-1">Instagram</label>
                        <input type="text" name="instagram" id="instagram" 
                            class="border px-4 py-2 rounded-lg w-full" 
                            value="{{ old('instagram') }}" 
                            placeholder="https://instagram.com/username">
                    </div>
                    <div class="flex flex-col w-1/2">
                        <label for="whatsapp" class="text-sm font-medium mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp" 
                            class="border px-4 py-2 rounded-lg w-full" 
                            value="{{ old('whatsapp') }}" 
                            placeholder="https://wa.me/6281234567890">
                        </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-4 flex items-start">
                <label for="description" class="w-1/4 font-medium pt-2">Deskripsi</label>
                <textarea name="description" id="description" rows="3" class="border px-4 py-2 rounded-lg w-3/4" required>{{ old('description') }}</textarea>
            </div>

            {{-- Harga Tiket --}}
            <div class="mb-4 flex items-center">
                <label class="w-1/4 font-medium">Harga Tiket</label>
                <div class="flex items-center gap-2 w-3/4">
                    <div class="w-full">
                        <label for="price_weekday" class="block font-medium mb-1">Weekday</label>
                        <input type="text" name="price_weekday" id="price_weekday"
                            class="border px-4 py-2 rounded-lg w-full rupiah-input" value="{{ old('price_weekday') }}" required placeholder="Contoh: 50.000">
                    </div>
                    <div class="text-xl font-semibold text-gray-600 pt-6">/</div>
                    <div class="w-full">
                        <label for="price_weekend" class="block font-medium mb-1">Weekend</label>
                        <input type="text" name="price_weekend" id="price_weekend"
                            class="border px-4 py-2 rounded-lg w-full rupiah-input" value="{{ old('price_weekend') }}" required placeholder="Contoh: 75.000">
                    </div>
                </div>
            </div>
            
            {{-- Jam Operasional --}}
            <div class="mb-4 flex items-center">
                <label class="w-1/4 font-medium">Jam Operasional</label>
                <div class="flex items-center gap-2 w-3/4">
                    <div class="w-full">
                        <label for="open_time" class="block font-medium mb-1">Jam Buka</label>
                        <input type="time" name="open_time" id="open_time" class="border px-4 py-2 rounded-lg w-full" value="{{ old('open_time') }}">
                    </div>
                    <div class="text-xl font-semibold text-gray-600 pt-6">-</div>
                    <div class="w-full">
                        <label for="close_time" class="block font-medium mb-1">Jam Tutup</label>
                        <input type="time" name="close_time" id="close_time" class="border px-4 py-2 rounded-lg w-full" value="{{ old('close_time') }}">
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="mb-4 flex items-center">
                <label for="location" class="w-1/4 font-medium">Lokasi</label>
                <input type="text" name="location" id="location" class="border px-4 py-2 rounded-lg w-3/4" value="{{ old('location') }}" required>
            </div>

            <div class="mb-4 flex items-center">
                <label for="location_embed" class="w-1/4 font-medium">Lokasi GMAPS</label>
                <input type="textarea" placeholder='<iframe src="..."></iframe>' name="location_embed" id="location_embed" class="border px-4 py-2 rounded-lg w-3/4" value="{{ old('location_embed') }}" required>
            </div>

            <div class="mt-8 mb-8 p-6 bg-blue-50 dark:bg-slate-800/50 border border-blue-100 dark:border-slate-700 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-bold text-blue-700 dark:text-blue-400">Data Pendukung Rekomendasi Lokasi</h4>
                    <span class="px-2 py-1 bg-blue-200 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-[10px] font-bold rounded uppercase">Digunakan untuk SPK</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-6">
                    Informasi ini digunakan sistem untuk menghitung rekomendasi lokasi berdasarkan kapasitas, tipe lokasi, dan karakteristik indoor/outdoor.
                </p>

                <div class="space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center">
                        <div class="w-full md:w-1/4">
                            <label for="capacity" class="font-medium text-gray-700 dark:text-slate-300">Kapasitas Maksimal Peserta</label>
                            <p class="text-[10px] text-gray-500 dark:text-slate-500 italic">Maksimal daya tampung lokasi.</p>
                        </div>
                        <div class="w-full md:w-3/4">
                            <input type="number" name="capacity" id="capacity" 
                                class="border px-4 py-2 rounded-lg w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" 
                                value="{{ old('capacity') }}" placeholder="Contoh: 150">
                            <p class="text-[10px] text-blue-500 mt-1">Isi perkiraan jumlah peserta maksimal yang dapat ditampung lokasi.</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <div class="w-full md:w-1/4">
                            <label for="venue_type" class="font-medium text-gray-700 dark:text-slate-300">Tipe Lokasi</label>
                            <p class="text-[10px] text-gray-500 dark:text-slate-500 italic">Kategori area.</p>
                        </div>
                        <div class="w-full md:w-3/4">
                            <input type="text" name="venue_type" id="venue_type" 
                                class="border px-4 py-2 rounded-lg w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" 
                                value="{{ old('venue_type') }}" placeholder="Contoh: aula, taman, lapangan, pendopo">
                            <p class="text-[10px] text-blue-500 mt-1">Digunakan untuk mencocokkan jenis kegiatan dan preferensi lokasi.</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center">
                        <div class="w-full md:w-1/4">
                            <label class="font-medium text-gray-700 dark:text-slate-300">Karakteristik Lokasi</label>
                            <p class="text-[10px] text-gray-500 dark:text-slate-500 italic">Indoor / Outdoor.</p>
                        </div>
                        <div class="w-full md:w-3/4">
                            <div class="flex gap-8 items-center h-10">
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_indoor" value="1" {{ old('is_indoor') ? 'checked' : '' }} 
                                        class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                    <span class="ml-2 text-gray-700 dark:text-slate-300 group-hover:text-blue-600 transition-colors">Indoor</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_outdoor" value="1" {{ old('is_outdoor') ? 'checked' : '' }} 
                                        class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                    <span class="ml-2 text-gray-700 dark:text-slate-300 group-hover:text-blue-600 transition-colors">Outdoor</span>
                                </label>
                            </div>
                            <p class="text-[10px] text-blue-500 mt-1">Pilih salah satu atau keduanya jika lokasi dapat digunakan indoor dan outdoor.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upload Gambar --}}
            <div class="mb-4 flex items-center">
                <label for="image" class="w-1/4 font-medium">Upload Gambar</label>
                <input type="file" name="image" id="image" accept="image/*" class="border px-4 py-2 rounded-lg w-3/4">
            </div>

            {{-- Tombol --}}
            <div class="flex justify-between mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">Simpan</button>
                <a href="{{ route('content.index') }}" class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded-md">Kembali</a>
            </div>
        </form>
    </div>
</main>

<script>
    // Format rupiah saat mengetik
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('rupiah-input')) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = formatRupiah(value);
        }
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        let number_string = angka.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    // Bersihkan format titik sebelum submit
    document.getElementById('form-content').addEventListener('submit', function() {
        document.querySelectorAll('.rupiah-input').forEach(input => {
            input.value = input.value.replace(/\D/g, '');
        });
    });
</script>

@if(session('error'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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