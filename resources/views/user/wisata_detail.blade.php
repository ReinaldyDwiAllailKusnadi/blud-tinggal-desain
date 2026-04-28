@extends('layouts.info')

@section('title', 'BLUD Pariwisata')
@section('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')

@section('content')
<section class="py-10 px-6">
    <h1 class="bg-primary mx-auto w-max text-center px-8 py-2 rounded-2xl uppercase text-white font-bold text-lg mb-8">
        {{ $contents->name }}
    </h1>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Gambar Wisata --}}
        @if($contents->image)
            <div>
                <img src="{{ asset($contents->image) }}" alt="{{ $contents->name }}"
                     class="w-full rounded-xl shadow-md object-cover max-h-[500px]">
            </div>
        @endif

        {{-- Sosial Media --}}
        @if($contents->instagram || $contents->tiktok)
            <div class="bg-white border rounded-xl shadow p-6">
                <h2 class="font-bold text-xl text-gray-800 mb-4">Sosial Media</h2>
                <div class="flex flex-col gap-3">
                    @if($contents->instagram)
                        <a href="{{ $contents->instagram }}" target="_blank" 
                        class="flex items-center gap-3 text-pink-600 hover:text-pink-700 font-medium transition-colors">
                            {{-- Icon Instagram --}}
                            <img src="{{ asset('assets/img/instagram.png') }}" alt="Instagram" 
                                class="w-6 h-6 object-contain shrink-0">
                            <span>Instagram</span>
                        </a>
                    @endif

                    @if($contents->whatsapp)
                        <a href="{{ $contents->whatsapp }}" target="_blank" 
                        class="flex items-center gap-3 text-green-800 hover:text-green font-medium transition-colors">
                            <img src="{{ asset('assets/img/whatsapp.png') }}" alt="whatsapp" 
                                class="w-6 h-6 object-contain shrink-0">
                            <span>WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif
        {{-- Deskripsi --}}
        <div class="bg-white border rounded-xl shadow p-6">
            <h2 class="font-bold text-xl text-gray-800 mb-4">Deskripsi</h2>
            <p class="text-gray-600 leading-relaxed">
                {{ $contents->description ?? 'Tidak ada deskripsi untuk wisata ini.' }}
            </p>
        </div>

        {{-- Jam Operasional & Harga --}}
        <div class="bg-white/80 backdrop-blur-sm border rounded-2xl shadow p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">
                Jam Operasional & Harga Tiket
            </h2>

            <div class="space-y-4">
                {{-- Jam Operasional --}}
                <p class="flex items-center text-gray-700 bg-yellow-50 px-4 py-2 rounded-xl">
                    <strong class="mr-2">Jam Buka:</strong>
                    {{ $contents->open_time ? \Carbon\Carbon::parse($contents->open_time)->format('H:i') : '-' }}
                    -
                    {{ $contents->close_time ? \Carbon\Carbon::parse($contents->close_time)->format('H:i') : '-' }}
                </p>

                {{-- Harga Weekday & Weekend --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <p class="text-gray-700 bg-blue-50 px-4 py-2 rounded-xl">
                        <strong>Weekday:</strong> Rp{{ $contents->price_weekday ?? '-' }}
                    </p>
                    <p class="text-gray-700 bg-green-50 px-4 py-2 rounded-xl">
                        <strong>Weekend:</strong> Rp{{ $contents->price_weekend ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Lokasi --}}
        <div class="bg-white border rounded-xl shadow p-6">
            <h2 class="font-bold text-xl text-gray-800 mb-4">Lokasi</h2>
            @if ($contents->location)
                <p class="text-gray-700 font-medium mb-4">{{ $contents->location }}</p>
            @endif

            @if ($contents->location_embed)
                <div class="relative w-full pt-[56.25%] overflow-hidden rounded-lg">
                    {!! str_replace(
                        '<iframe ',
                        '<iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" ',
                        $contents->location_embed
                    ) !!}
                </div>
            @else
                <p class="text-gray-500 italic">Peta lokasi belum tersedia.</p>
            @endif
        </div>
        <div class="flex justify-end">
            <a href="{{ route('fasilitas', $contents->slug) }}" 
            class="group flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 ease-out">
                
                <span class="font-medium text-sm tracking-wide">Info Lebih Lanjut</span>
                
                <div class="bg-white/20 group-hover:bg-white/30 w-7 h-7 rounded-full flex items-center justify-center transition-all duration-300">
                    <svg class="transform group-hover:translate-x-1 transition-transform duration-300" 
                        width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M5 3L9 7L5 11" stroke="white" stroke-width="2" 
                            stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </a>
        </div>

    </div>
</section>
@endsection


