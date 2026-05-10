@extends('layouts.sidebar')

@section('content')
<div class="max-w-6xl mx-auto mt-10 pb-20">
    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-200 rounded text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-200 rounded text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- FORM UTAMA --}}
    <form action="{{ route('content.update', $content->id) }}" method="POST" enctype="multipart/form-data" id="form-content">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow transition-colors duration-200">
            <h2 class="text-xl font-semibold mb-6 dark:text-white">Edit Tempat Wisata</h2>

            <div class="mb-4">
                <label class="block font-medium dark:text-slate-200">Nama Tempat</label>
                <input type="text" name="name" value="{{ old('name', $content->name) }}"
                    class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">
            </div>
            
            <div class="mb-4">
                <label class="block font-medium mb-2 dark:text-slate-200">Sosial Media</label>
                <div class="flex gap-4">
                    <!-- Kolom Instagram -->
                    <div class="flex flex-col w-1/2">
                        <label for="instagram" class="text-sm font-medium mb-1 dark:text-slate-400">Instagram</label>
                        <input type="text" name="instagram" id="instagram"
                            value="{{ old('instagram', $content->instagram) }}"
                            class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white" 
                            placeholder="https://instagram.com/username">
                    </div>

                    <!-- Kolom WhatsApp -->
                    <div class="flex flex-col w-1/2">
                        <label for="whatsapp" class="text-sm font-medium mb-1 dark:text-slate-400">WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp"
                            value="{{ old('whatsapp', $content->whatsapp) }}"
                            class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white" 
                            placeholder="https://wa.me/6281234567890">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium dark:text-slate-200">Deskripsi</label>
                <textarea name="description" class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">{{ old('description', $content->description) }}</textarea>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label class="block font-medium dark:text-slate-200">Harga Weekday</label>
                    <input type="text" name="price_weekday" value="{{ old('price_weekday', number_format((int) $content->price_weekday, 0, ',', '.')) }}"
                        class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white rupiah-input">
                </div>
                <div class="w-1/2">
                    <label class="block font-medium dark:text-slate-200">Harga Weekend</label>
                    <input type="text" name="price_weekend" value="{{ old('price_weekend', number_format((int) $content->price_weekend, 0, ',', '.')) }}"
                        class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white rupiah-input">
                </div>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label class="block font-medium dark:text-slate-200">Jam Buka</label>
                    <input type="time" name="open_time"  value="{{ old('open_time', \Carbon\Carbon::parse($content->open_time)->format('H:i')) }}"
                        class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">
                </div>
                <div class="w-1/2">
                    <label class="block font-medium dark:text-slate-200">Jam Tutup</label>
                    <input type="time" name="close_time"  value="{{ old('close_time', \Carbon\Carbon::parse($content->close_time)->format('H:i')) }}"
                        class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium dark:text-slate-200">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $content->location) }}"
                    class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">
            </div>

            <div class="mb-4">
                <label class="block font-medium dark:text-slate-200">Lokasi GMAPS</label>
                <input type="text" name="location_embed" value="{{ old('location_embed', $content->location_embed) }}"
                    class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">
            </div>

            <div class="mb-6">
                <label class="block font-medium dark:text-slate-200">Gambar</label>
                <input type="file" name="image" class="w-full border px-4 py-2 rounded dark:bg-slate-900 dark:border-slate-700 dark:text-white">
                @if($content->image)
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-1">Gambar saat ini:</p>
                        <img src="{{ str_starts_with($content->image, 'assets/img/') ? asset($content->image) : asset('storage/' . $content->image) }}" class="w-48 rounded shadow">
                    </div>
                @endif
            </div>

            {{-- DATA PENDUKUNG SPK --}}
            <div class="mt-8 mb-8 p-6 bg-blue-50 dark:bg-slate-800/50 border border-blue-100 dark:border-slate-700 rounded-xl transition-colors duration-200">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-bold text-blue-700 dark:text-blue-400">Data Pendukung Rekomendasi Lokasi</h4>
                    <span class="px-2 py-1 bg-blue-200 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-[10px] font-bold rounded uppercase">Digunakan untuk SPK</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-6">
                    Informasi ini digunakan sistem untuk menghitung rekomendasi lokasi berdasarkan kapasitas, tipe lokasi, dan karakteristik indoor/outdoor.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300">Kapasitas Maksimal Peserta</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $content->capacity ?? '') }}"
                            class="w-full border px-4 py-2 rounded-lg mt-1 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: 150">
                        <p class="text-[10px] text-blue-500 mt-1 italic">Isi perkiraan jumlah peserta maksimal yang dapat ditampung lokasi.</p>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300">Tipe Lokasi</label>
                        <input type="text" name="venue_type" value="{{ old('venue_type', $content->venue_type ?? '') }}"
                            class="w-full border px-4 py-2 rounded-lg mt-1 dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: aula, taman, lapangan, pendopo">
                        <p class="text-[10px] text-blue-500 mt-1 italic">Digunakan untuk mencocokkan jenis kegiatan dan preferensi lokasi.</p>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="block font-medium text-gray-700 dark:text-slate-300 mb-2">Karakteristik Lokasi</label>
                    <div class="flex gap-8 items-center bg-white dark:bg-slate-900/50 p-3 rounded-lg border border-gray-100 dark:border-slate-700">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_indoor" value="1" {{ old('is_indoor', $content->is_indoor ?? false) ? 'checked' : '' }} 
                                class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                            <span class="ml-2 text-gray-700 dark:text-slate-300 group-hover:text-blue-600 transition-colors">Indoor</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_outdoor" value="1" {{ old('is_outdoor', $content->is_outdoor ?? false) ? 'checked' : '' }} 
                                class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                            <span class="ml-2 text-gray-700 dark:text-slate-300 group-hover:text-blue-600 transition-colors">Outdoor</span>
                        </label>
                    </div>
                    <p class="text-[10px] text-blue-500 mt-2">Pilih salah satu atau keduanya jika lokasi dapat digunakan indoor dan outdoor.</p>
                </div>
            </div>

            <hr class="my-8 border-gray-200 dark:border-slate-700">

            {{-- FASILITAS & HARGA --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-xl font-bold text-gray-800 dark:text-slate-50">Fasilitas & Harga Sewa</h4>
                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-[10px] font-bold rounded uppercase">Data Rekomendasi</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-slate-400 mb-8 pb-4">
                    Data fasilitas dan harga digunakan untuk mencocokkan kebutuhan pemohon dengan fasilitas yang tersedia di setiap lokasi.
                </p>

                {{-- Pricelist --}}
                <div class="mb-10">
                    <div class="flex items-center gap-2 mb-2">
                        <label class="block font-bold text-gray-700 dark:text-slate-200">Penyewaan / Harga Area</label>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-4 italic">Isi area atau bagian lokasi yang dapat disewa, beserta luas dan harga sewanya.</p>
                    
                    <div id="price-list" class="overflow-x-auto">
                        <table class="min-w-full border dark:border-slate-700 rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-700 dark:text-slate-200">
                                    <th class="px-3 py-3 text-left font-semibold text-sm">Bagian / Area</th>
                                    <th class="px-3 py-3 text-left font-semibold text-sm">Luas Area</th>
                                    <th class="px-3 py-3 text-left font-semibold text-sm">Harga Sewa (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($content->features->where('type', 'price') as $i => $feature)
                                    <tr class="border-b dark:border-slate-700">
                                        <input type="hidden" name="features[{{ $i }}][id]" value="{{ $feature->id }}">
                                        <input type="hidden" name="features[{{ $i }}][type]" value="price">
                                        <td class="px-3 py-2">
                                            <input type="text" name="features[{{ $i }}][bagian]" value="{{ $feature->bagian }}"
                                                class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Pendopo, Area Tengah, Lapangan">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="features[{{ $i }}][luas]" value="{{ $feature->luas }}"
                                                class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: 20x20 m atau 400 m²">
                                        </td>
                                        <td class="px-3 py-2 flex items-center gap-2">
                                            <input type="text" name="features[{{ $i }}][price]" value="{{ number_format((int) $feature->price, 0, ',', '.') }}"
                                                class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50 rupiah-input" placeholder="Contoh: 100.000">
                                            <button type="button" onclick="this.closest('tr').remove()" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded transition-all">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" onclick="addBagian()" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm">
                        + Tambah Area Sewa
                    </button>
                </div>

                {{-- Fasilitas --}}
                <div class="mb-10">
                    <label class="block font-bold text-gray-700 dark:text-slate-200 mb-1">Fasilitas Umum</label>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-4 italic">Contoh: parkir, toilet, aula, kursi, sound system, panggung.</p>

                    <div id="facility-list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @if(old('facility_names'))
                            @foreach(old('facility_names') as $name)
                                <div class="flex gap-2">
                                    <input type="text" name="facility_names[]" value="{{ $name }}" class="border rounded-lg px-3 py-2 w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Nama fasilitas" required>
                                    <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-all">Hapus</button>
                                </div>
                            @endforeach
                        @elseif($content->features->where('type', 'facility')->count())
                            @foreach($content->features->where('type', 'facility') as $feature)
                                <div class="flex gap-2">
                                    <input type="text" name="facility_names[]" value="{{ $feature->facility_name }}" class="border rounded-lg px-3 py-2 w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Toilet Umum" required>
                                    <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-all">Hapus</button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex gap-2">
                                <input type="text" name="facility_names[]" class="border rounded-lg px-3 py-2 w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Toilet Umum" required>
                                <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-all">Hapus</button>
                            </div>
                        @endif
                    </div>

                    <button type="button" onclick="addFacilityInput()" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm">
                        + Tambah Fasilitas
                    </button>
                </div>
            </div>

            <div class="flex justify-end mt-12 border-t dark:border-slate-700 pt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-3 rounded-lg font-bold shadow-lg transition-all transform hover:scale-105">
                    Simpan Semua Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

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

    function addBagian() {
        const tbody = document.querySelector('#price-list tbody');

        const tr = document.createElement('tr');
        tr.className = 'border-b dark:border-slate-700';
        tr.innerHTML = `
            <input type="hidden" name="features[${priceIndex}][type]" value="price">
            <td class="px-3 py-2">
                <input type="text" name="features[${priceIndex}][bagian]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Pendopo, Area Tengah, Lapangan" required>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="features[${priceIndex}][luas]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: 20x20 m atau 400 m²" required>
            </td>
            <td class="px-3 py-2 flex items-center gap-2">
                <input type="text" name="features[${priceIndex}][price]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50 rupiah-input" placeholder="Contoh: 100.000" required>
                <button type="button" onclick="this.closest('tr').remove()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-all">Hapus</button>
            </td>
        `;

        tbody.appendChild(tr);
        priceIndex++;
    }

    function addFacilityInput() {
        const container = document.getElementById('facility-list');
        const div = document.createElement('div');
        div.className = 'flex gap-2 mt-2';
        div.innerHTML = `
            <input type="text" name="facility_names[]" class="border rounded-lg px-3 py-2 w-full dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Toilet Umum" required>
            <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-all">Hapus</button>
        `;
        container.appendChild(div);
    }
    
    let priceIndex = {{ $content->features->where('type', 'price')->count() }};
</script>
@endsection
