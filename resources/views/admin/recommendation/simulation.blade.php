@extends('layouts.sidebar')

@section('content')
<main class="p-6 bg-gray-50 dark:bg-slate-900 flex-1 transition-colors duration-200">
    <div class="mb-6">
        <h3 class="text-2xl font-semibold text-gray-800 dark:text-slate-50">Simulasi Rekomendasi Lokasi</h3>
        <p class="text-gray-600 dark:text-slate-400">Gunakan halaman ini untuk menguji algoritma SPK Knowledge-Based Recommendation berbasis similarity.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-md transition-colors duration-200">
        <form action="{{ route('admin.recommendation.simulate') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kiri --}}
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300 mb-1">Jenis Kegiatan</label>
                        <input type="text" name="event_type" value="{{ $criteria['event_type'] ?? '' }}" 
                            class="w-full border px-4 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" 
                            placeholder="Contoh: seminar, bazar, outbound">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300 mb-1">Jumlah Peserta</label>
                        <input type="number" name="participants" value="{{ $criteria['participants'] ?? '' }}" 
                            class="w-full border px-4 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" 
                            placeholder="Contoh: 100">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300 mb-1">Tanggal Kegiatan</label>
                        <input type="date" name="date" value="{{ $criteria['date'] ?? date('Y-m-d') }}" 
                            class="w-full border px-4 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" required>
                    </div>
                </div>

                {{-- Kanan --}}
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300 mb-1">Budget (Rp)</label>
                        <input type="number" name="budget" value="{{ $criteria['budget'] ?? '' }}" 
                            class="w-full border px-4 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" 
                            placeholder="Contoh: 2000000">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300 mb-1">Fasilitas Dibutuhkan (Pisahkan dengan koma)</label>
                        <input type="text" name="facilities" value="{{ isset($criteria['facilities']) ? implode(', ', $criteria['facilities']) : '' }}" 
                            class="w-full border px-4 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" 
                            placeholder="Contoh: aula, kursi, sound system">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-slate-300 mb-1">Preferensi Lokasi</label>
                        <select name="preference" class="w-full border px-4 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50">
                            <option value="">Tidak ada preferensi</option>
                            <option value="indoor" {{ (isset($criteria['preference']) && $criteria['preference'] == 'indoor') ? 'selected' : '' }}>Indoor</option>
                            <option value="outdoor" {{ (isset($criteria['preference']) && $criteria['preference'] == 'outdoor') ? 'selected' : '' }}>Outdoor</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-semibold shadow transition-all">
                    Proses Rekomendasi
                </button>
            </div>
        </form>
    </div>

    @if(isset($results))
    <div class="mt-10">
        <h4 class="text-xl font-bold mb-4 text-gray-800 dark:text-slate-50">Hasil Rekomendasi</h4>
        <div class="bg-white dark:bg-slate-800 shadow-md rounded-lg overflow-hidden transition-colors duration-200">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-500 dark:bg-slate-700 text-white">
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Nama Lokasi</th>
                        <th class="p-3 text-center">Skor</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-left">Detail Fisik</th>
                        <th class="p-3 text-left">Alasan & Kecocokan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $index => $item)
                    <tr class="border-b dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <td class="p-3 align-top text-gray-800 dark:text-slate-300">{{ $index + 1 }}</td>
                        <td class="p-3 align-top">
                            <span class="font-bold text-gray-800 dark:text-slate-50">{{ $item['name'] }}</span>
                            <br>
                            <span class="text-xs text-gray-500">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                        </td>
                        <td class="p-3 align-top text-center">
                            <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $item['score'] }}</div>
                        </td>
                        <td class="p-3 align-top text-center">
                            @php
                                $badgeClass = match($item['status']) {
                                    'Sangat Direkomendasikan' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'Direkomendasikan' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'Cukup Sesuai' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'Kurang Direkomendasikan' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'Tidak Tersedia' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $badgeClass }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="p-3 align-top text-sm text-gray-600 dark:text-slate-300">
                            <strong>Kapasitas:</strong> {{ $item['capacity'] ?? '-' }}<br>
                            <strong>Tipe:</strong> {{ $item['venue_type'] ?? '-' }}<br>
                            <strong>Karakter:</strong> {{ $item['is_indoor'] ? 'Indoor' : '' }} {{ $item['is_outdoor'] ? 'Outdoor' : '' }}
                        </td>
                        <td class="p-3 align-top">
                            <ul class="text-xs space-y-1 list-disc list-inside text-gray-700 dark:text-slate-400">
                                @foreach($item['reasons'] as $reason)
                                    <li>{{ $reason }}</li>
                                @endforeach
                            </ul>
                            @if(!empty($item['matched_facilities']))
                                <div class="mt-2">
                                    <span class="text-[10px] font-bold uppercase text-gray-400">Fasilitas Cocok:</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($item['matched_facilities'] as $fac)
                                            <span class="bg-blue-50 dark:bg-slate-700 text-blue-700 dark:text-blue-300 px-1 rounded text-[10px] border border-blue-200 dark:border-slate-600">
                                                {{ $fac }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">Tidak ada data lokasi yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</main>
@endsection
