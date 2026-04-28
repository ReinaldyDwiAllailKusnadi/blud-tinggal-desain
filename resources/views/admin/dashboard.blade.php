@extends('layouts.sidebar')

@section('content')
<main class="p-6 bg-gray-100 dark:bg-gray-900 transition-colors duration-200 text-gray-800 dark:text-gray-200 flex-1">
  <h1 class="text-2xl font-bold mb-6">Selamat Datang, {{ auth()->user()->name }}.</h1>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Grafik Pengajuan --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow h-[300px]">
      <h2 class="text-lg font-semibold mb-4">Statistik Pengajuan (6 Bulan Terakhir)</h2>
      <canvas id="submissionChart" height="125"></canvas>
    </div>

    {{-- Kalender --}}
    @php
    use Carbon\Carbon;

    $today = Carbon::now();
    $displayMonth = Carbon::now(); // bisa diganti dengan request('month') jika pakai navigasi bulan

    $startOfMonth = $displayMonth->copy()->startOfMonth();
    $daysInMonth = $displayMonth->daysInMonth;

    // Hari pertama dalam minggu (1 = Senin, 7 = Minggu)
    $firstDayOfWeek = $startOfMonth->dayOfWeekIso;
    @endphp

    <div class="bg-blue-50 dark:bg-gray-800 p-4 rounded-xl shadow h-[300px]">
    <h2 class="text-lg font-semibold mb-4 text-center">
        Kalender - {{ $displayMonth->isoFormat('MMMM Y') }}
    </h2>

    <div class="grid grid-cols-7 text-center text-sm font-medium text-gray-600 dark:text-gray-400 gap-y-2">
        {{-- Header hari --}}
        <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div>M</div>

        @for ($i = 1; $i < $firstDayOfWeek; $i++)
        <div></div>
        @endfor

        {{-- Loop tanggal --}}
        @for ($day = 1; $day <= $daysInMonth; $day++)
        @php
            $date = $displayMonth->copy()->day($day);
            $isToday = $date->isSameDay($today);
        @endphp
        <div class="py-1 {{ $isToday ? 'bg-blue-500 text-white font-bold rounded-full' : '' }}">
            {{ $day }}
        </div>
        @endfor
    </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="bg-sky-600 p-4 rounded-xl shadow text-white h-[250px] overflow-hidden">
      <h2 class="text-lg font-bold mb-3">Aktivitas Terakhir Admin</h2>
      <ul class="space-y-2 max-h-[180px] overflow-y-auto pr-1 text-sm">
        @forelse($activities as $activity)
          <li class="flex justify-between items-start border-b border-white/20 pb-1">
            <span>• {{ $activity->admin->name ?? '-' }} {{ $activity->description }}</span>
            <span class="text-xs text-white/70 whitespace-nowrap">
              {{ $activity->created_at->diffForHumans() }}
            </span>
          </li>
        @empty
          <li class="text-white/80">Belum ada aktivitas.</li>
        @endforelse
      </ul>
    </div>

    {{-- Jumlah Akun --}}
    <div class="bg-cyan-400 p-6 rounded-xl text-white shadow flex items-center justify-center h-[250px]">
      <div class="text-center">
        <h2 class="text-lg mb-2">Akun terdaftar</h2>
        <div class="text-6xl font-bold">{{ $userCount }}</div>
      </div>
    </div>
  </div>

  {{-- Chart.js --}}
  <script>
    const ctx = document.getElementById('submissionChart').getContext('2d');
    const submissionChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($dates) !!},
        datasets: [{
          label: 'Jumlah Pengajuan',
          data: {!! json_encode($counts) !!},
          backgroundColor: '#0284c7'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  </script>
</main>
@endsection
