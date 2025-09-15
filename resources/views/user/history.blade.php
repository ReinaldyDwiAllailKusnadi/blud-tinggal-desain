@extends('layouts.info')

@section('title', 'BLUD Pariwisata')
@section('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')

@section('content')
<section class="py-10 px-6">
  <h1 class="bg-primary mx-auto w-max text-center px-8 py-2 rounded-2xl uppercase text-white font-bold text-lg mb-8">
    Riwayat Pengajuan
  </h1>

  <div class="bg-white shadow-md rounded-lg p-4 mt-6">
    {{-- Untuk layar besar --}}
    <div class="hidden lg:block overflow-x-auto">
      <table class="min-w-full border-collapse">
        <thead>
          <tr class="bg-primary text-white">
            <th class="p-3">No</th>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Vendor</th>
            <th class="p-3">Lokasi</th>
            <th class="p-3">Kegiatan</th>
            <th class="p-3">Dokumen</th>
            <th class="p-3">Status</th>
            <th class="p-3">Catatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($submissions as $sub)
          <tr class="border-b">
            <td class="p-3 text-center">{{ $loop->iteration }}</td>
            <td class="p-3">{{ $sub->apply_date }}</td>
            <td class="p-3">{{ $sub->vendor }}</td>
            <td class="p-3">{{ $sub->location }}</td>
            <td class="p-3">{{ $sub->name_event }}</td>
            <td class="p-3 space-y-1">
              @if($sub->file)
              <a href="{{ asset('storage/' . $sub->file) }}" class="text-blue-600 underline block">📄 File Proposal</a>
              @endif
              @if($sub->appl_letter)
              <a href="{{ asset('storage/' . $sub->appl_letter) }}" class="text-blue-600 underline block">📑 File Pengajuan</a>
              @endif
              @if($sub->ktp)
              <a href="{{ asset('storage/' . $sub->ktp) }}" class="text-blue-600 underline block">🆔 File KTP</a>
              @endif
              @if($sub->actv_letter)
              <a href="{{ asset('storage/' . $sub->actv_letter) }}" class="text-blue-600 underline block">📑 File Kegiatan / Rundown</a>
              @endif
            </td>
            <td class="p-3 text-center font-semibold">
              @if($sub->status == 'pending')
              <span class="text-yellow-600">Pending</span>
              @elseif($sub->status == 'approved')
              <span class="text-green-600">Disetujui</span>
              @else
              <span class="text-red-600">Ditolak</span>
              @endif
            </td>
            <td class="p-3">{{ $sub->notes ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-6 text-gray-500">Data pengajuan tidak ada.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Mobile --}}
    <div class="space-y-4 lg:hidden mt-6">
      @forelse ($submissions as $sub)
      <div class="rounded-2xl border border-gray-200 bg-gray-50 shadow p-5">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-3">
          <div class="text-sm text-gray-500 font-medium">#{{ $loop->iteration }}</div>
          <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($sub->apply_date)->translatedFormat('d M Y') }}</div>
        </div>

        {{-- Lokasi dan Vendor --}}
        <div class="mb-2">
          <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">📍 Lokasi:</span> {{ $sub->location }}</p>
          <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">🧾 Vendor:</span> {{ $sub->vendor }}</p>
        </div>

        {{-- Kegiatan --}}
        <div class="mb-3">
          <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">🎯 Kegiatan:</span> {{ $sub->name_event }}</p>
        </div>

        {{-- Dokumen --}}
        <div class="mb-3 space-y-1">
          @if($sub->file)
          <a href="{{ asset('storage/' . $sub->file) }}" class="text-blue-600 text-sm underline block">📄 File Proposal</a>
          @endif
          @if($sub->appl_letter)
          <a href="{{ asset('storage/' . $sub->appl_letter) }}" class="text-blue-600 text-sm underline block">📄 File Pengajuan</a>
          @endif
          @if($sub->ktp)
          <a href="{{ asset('storage/' . $sub->ktp) }}" class="text-blue-600 text-sm underline block">🆔 KTP</a>
          @endif
           @if($sub->actv_letter)
          <a href="{{ asset('storage/' . $sub->actv_letter) }}" class="text-blue-600 text-sm underline block">📑 File Kegiatan / Rundown</a>
          @endif
        </div>

        {{-- Status --}}
        <div class="mb-2">
          <p class="text-sm">
            <span class="font-semibold text-gray-700">📌 Status:</span>
            @if($sub->status == 'pending')
            <span class="text-yellow-600 font-semibold">Pending</span>
            @elseif($sub->status == 'approved')
            <span class="text-green-600 font-semibold">Disetujui</span>
            @else
            <span class="text-red-600 font-semibold">Ditolak</span>
            @endif
          </p>
        </div>

        {{-- Catatan --}}
        <div>
          <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">📝 Catatan:</span> {{ $sub->notes ?? '-' }}</p>
        </div>
      </div>
      @empty
      <p class="text-center text-gray-500 py-6">Data pengajuan tidak ada.</p>
      @endforelse
    </div>    
  </div>
  <div class="flex justify-center mt-10">
    <a href="{{ route('submission') }}"
       class="group bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3 rounded-full shadow-md hover:shadow-lg transition-all duration-300 ease-out">
        
        <span class="font-semibold text-sm tracking-wide">
            Buat Pengajuan Lagi
        </span>
    </a>
  </div>
</section>

<script>
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

