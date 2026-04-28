@extends('layouts.sidebar')

@section('content')
<div class="max-w-6xl mx-auto mt-10">
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

    {{-- FORM EDIT KONTEN --}}
    <form action="{{ route('content.update', $content->id) }}" method="POST" enctype="multipart/form-data" id="form-content">
        @csrf
        @method('PUT')
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Edit Tempat Wisata</h2>

            <div class="mb-4">
                <label class="block font-medium">Nama Tempat</label>
                <input type="text" name="name" value="{{ old('name', $content->name) }}"
                    class="w-full border px-4 py-2 rounded">
            </div>
            
            <div class="mb-4">
                <label class="block font-medium mb-2">Sosial Media</label>
                <div class="flex gap-4">
                    <!-- Kolom Instagram -->
                    <div class="flex flex-col w-1/2">
                        <label for="instagram" class="text-sm font-medium mb-1">Instagram</label>
                        <input type="text" name="instagram" id="instagram"
                            value="{{ old('instagram', $content->instagram) }}"
                            class="w-full border px-4 py-2 rounded" 
                            placeholder="https://instagram.com/username">
                    </div>

                    <!-- Kolom TikTok -->
                    <div class="flex flex-col w-1/2">
                        <label for="whatsapp" class="text-sm font-medium mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp"
                            value="{{ old('whatsapp', $content->whatsapp) }}"
                            class="w-full border px-4 py-2 rounded" 
                            placeholder="https://wa.me/6281234567890">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Deskripsi</label>
                <textarea name="description" class="w-full border px-4 py-2 rounded">{{ old('description', $content->description) }}</textarea>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label class="block font-medium">Harga Weekday</label>
                    <input type="text" name="price_weekday" value="{{ old('price_weekday', $content->price_weekday) }}"
                        class="w-full border px-4 py-2 rounded">
                </div>
                <div class="w-1/2">
                    <label class="block font-medium">Harga Weekend</label>
                    <input type="text" name="price_weekend" value="{{ old('price_weekend', $content->price_weekend) }}"
                        class="w-full border px-4 py-2 rounded">
                </div>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="w-1/2">
                    <label class="block font-medium">Jam Buka</label>
                    <input type="time" name="open_time"  value="{{ old('open_time', \Carbon\Carbon::parse($content->open_time)->format('H:i')) }}"
                        class="w-full border px-4 py-2 rounded">
                </div>
                <div class="w-1/2">
                    <label class="block font-medium">Jam Tutup</label>
                    <input type="time" name="close_time"  value="{{ old('close_time', \Carbon\Carbon::parse($content->close_time)->format('H:i')) }}"
                        class="w-full border px-4 py-2 rounded">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $content->location) }}"
                    class="w-full border px-4 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Lokasi GMAPS</label>
                <input type="text" name="location_embed" value="{{ old('location_embed', $content->location_embed) }}"
                    class="w-full border px-4 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Gambar</label>
                <input type="file" name="image" class="w-full border px-4 py-2 rounded">
                @if($content->image)
                    <img src="{{ asset($content->image) }}" class="mt-2 w-40">
                @endif
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- FORM FITUR --}}
    <div class="bg-white p-6 rounded-lg shadow max-w-6xl mx-auto mt-6">
    <h4 class="text-xl font-semibold mb-4">
        Edit Fasilitas untuk: <span class="font-bold">{{ $content->name }}</span>
    </h4>

    <form action="{{ route('feature.update') }}" method="POST">
        @csrf
        <input type="hidden" name="location" value="{{ $content->id }}">

        {{-- Pricelist --}}
        <div class="mb-6">
            <label class="block font-semibold mb-2">Penyewaan</label>
            <div id="price-list" class="overflow-x-auto">
                <table class="min-w-full border rounded-lg">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-3 py-2 text-left">Bagian</th>
                            <th class="px-3 py-2 text-left">Luas</th>
                            <th class="px-3 py-2 text-left">Harga (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($content->features->where('type', 'price') as $i => $feature)
                            <tr class="border-b">
                                <input type="hidden" name="features[{{ $i }}][id]" value="{{ $feature->id }}">
                                <input type="hidden" name="features[{{ $i }}][type]" value="price">
                                <td class="px-3 py-2">
                                    <input type="text" name="features[{{ $i }}][bagian]" value="{{ $feature->bagian }}"
                                        class="border w-full px-3 py-2 rounded">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="features[{{ $i }}][luas]" value="{{ $feature->luas }}"
                                        class="border w-full px-3 py-2 rounded">
                                </td>
                                <td class="px-3 py-2 flex items-center gap-2">
                                    <input type="number" name="features[{{ $i }}][price]" value="{{ $feature->price }}"
                                        class="border w-full px-3 py-2 rounded">
                                    <button type="button" onclick="this.closest('tr').remove()" class="bg-red-500 text-white px-2 py-1 rounded">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="addBagian()" class="mt-3 bg-green-500 text-white px-4 py-2 rounded">
                Tambah Bagian
            </button>
        </div>

        {{-- Fasilitas --}}
        <div class="mb-6">
            <label class="block font-semibold mb-2">Fasilitas</label>

            <div id="facility-list" class="space-y-2">
                {{-- Jika ada input sebelumnya (setelah gagal validasi), tampilkan kembali --}}
                @if(old('facility_names'))
                    @foreach(old('facility_names') as $name)
                        <div class="flex gap-2">
                            <input type="text" name="facility_names[]" value="{{ $name }}" class="border rounded px-3 py-2 w-full" placeholder="Nama fasilitas" required>
                            <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </div>
                    @endforeach
                @elseif($content->features->where('type', 'facility')->count())
                    @foreach($content->features->where('type', 'facility') as $feature)
                        <div class="flex gap-2">
                            <input type="text" name="facility_names[]" value="{{ $feature->facility_name }}" class="border rounded px-3 py-2 w-full" placeholder="Nama fasilitas" required>
                            <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </div>
                    @endforeach
                @else
                    <div class="flex gap-2">
                        <input type="text" name="facility_names[]" class="border rounded px-3 py-2 w-full" placeholder="Nama fasilitas" required>
                        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </div>
                @endif
            </div>

            <button type="button" onclick="addFacilityInput()" class="mt-3 bg-green-500 text-white px-4 py-2 rounded">
                Tambah Fasilitas
            </button>
        </div>


        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                Simpan Fitur
            </button>
        </div>
    </form>
</div>
</div>
<script>
    function addBagian() {
        const tbody = document.querySelector('#price-list tbody');

        const tr = document.createElement('tr');
        tr.className = 'border-b';
        tr.innerHTML = `
            <input type="hidden" name="features[${priceIndex}][type]" value="price">
            <td class="px-3 py-2">
                <input type="text" name="features[${priceIndex}][bagian]" class="border w-full px-3 py-2 rounded" placeholder="Depan / Belakang / Samping" required>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="features[${priceIndex}][luas]" class="border w-full px-3 py-2 rounded" placeholder="10x10 / 20x20" required>
            </td>
            <td class="px-3 py-2 flex items-center gap-2">
                <input type="number" name="features[${priceIndex}][price]" class="border w-full px-3 py-2 rounded" placeholder="ex: 20000" required>
                <button type="button" onclick="this.closest('tr').remove()" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
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
            <input type="text" name="facility_names[]" class="border rounded px-3 py-2 w-full" placeholder="ex: Toilet Umum" required>
            <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
        `;
        container.appendChild(div);
    }
    
    let priceIndex = {{ $content->features->where('type', 'price')->count() }};
</script>
@endsection
