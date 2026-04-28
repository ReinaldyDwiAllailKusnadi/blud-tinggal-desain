@extends('layouts.info')

@section('title', 'BLUD Pariwisata')
@section('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')

@section('content')
<section class="py-10 px-6">
  <h1 class="bg-primary mx-auto w-max text-center px-8 py-2 rounded-2xl uppercase text-white font-bold text-lg mb-8">
      Jadwal Event
    </h1>
    <div class="grid grid-cols-1 gap-6 mt-10 lg:grid-cols-4 lg:gap-8">
      @forelse ($contents as $item)
          <a href="{{ route('booking', $item->slug) }}" class="block group">
            <div class="flex flex-col items-center bg-primary shadow-lg rounded-lg overflow-hidden transform transition duration-300 ease-in-out group-hover:scale-105 group-hover:shadow-2xl">
              <img class="w-full h-[230px] object-cover transition duration-300 group-hover:opacity-95" src="{{ asset($item->image) }}" alt="{{ $item->name }}">
              <div class="p-4 text-left w-full">
                <h2 class="font-bold text-white text-xl mb-2 transition duration-300 group-hover:text-white-300">
                  {{ $item->name }}
                </h2>
                <div class="flex justify-between items-center">
                  <p class="font-semibold text-white group-hover:text-white-200 transition duration-300">Jelajah Sekarang</p>
                  <img src="{{ asset('assets/svg/arrow-next-putih.svg') }}" alt="Next" class="transform transition duration-300 group-hover:translate-x-1">
                </div>
              </div>
            </div>
          </a>
      @empty
        <p class="text-gray-500 italic">Belum ada event tersedia.</p>
      @endforelse
    </div>

    <div class="py-20 px-4 md:px-12">
      <h1 class="font-bold text-2xl text-center mb-6">Rules Sewa</h1>

      <div class="bg-white rounded-xl p-6 shadow text-justify text-sm md:w-[80%] md:mx-auto">
        <ol class="list-decimal list-inside space-y-2 md:space-y-1.5 md:text-sm">
          <li>Pengguna dihimbau untuk login atau register terlebih dahulu di menu Login/register.</li>
          <li>Setelah menggunakan akun yang telah dibuat, pengguna dapat mengakses menu “Jadwal” pada halaman awal.</li>
          <li>Siapkan dokumen yang diperlukan seperti KTP, Surat Keterangan Kegiatan dan dokumen pendukung lainnya.</li>
          <li>Pengguna dapat memilih lokasi yang akan disewa di halaman “Jadwal”.</li>
          <li>Pengguna juga dapat melihat ketersediaan waktu untuk sewa dengan melihat jadwal event yang akan dilaksanakan pada masing-masing lokasi.</li>
          <li>Setelah cek jadwal dan ketersediaan waktu yang sesuai, pengguna dapat klik tombol “Book Now” pada halaman ini dan secara otomatis akan di arahkan ke halaman form penyewaan.</li>
          <li>Lanjutkan proses pengisian formulir hingga selesai dan “Kirim”.</li>
          <li>Selanjutnya pengguna dapat melakukan konfirmasi melalui e-mail atau nomor telepon Kantor BLUD Pariwisata untuk konfirmasi lebih lanjut.</li>
        </ol>
      </div>

      <div class="flex justify-end mt-10 md:mr-12">
        <a href="{{route('submission')}}">
          <p class="bg-primary text-white px-6 py-2 rounded-full shadow hover:bg-blue-600 transition">BOOK NOW!</p>
        </a>
      </div>
</section>

@endsection
