
<html lang="id" x-data="{ dark: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('dark', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': dark }">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="{{ asset('assets/js/index.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/input.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/output.css') }}"> 
  <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/svg/logo.svg') }}">

</head>
<body class="bg-gray-100 dark:bg-slate-900 transition-colors duration-200">
  <div class="flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-white dark:bg-slate-800 shadow-md p-4 flex justify-between items-center transition-colors duration-200">
    <div class="flex items-center gap-2">
      <img src="{{ asset('assets/img/logo blud.png') }}" alt="BLUD" class="h-10">
      <img src="{{ asset('assets/img/Telu.png') }}" alt="Telkom" class="h-12">
    </div>

    <div class="flex items-center gap-4">
      <button @click="dark = !dark" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none transition-colors">
        <!-- Sun icon -->
        <svg x-show="dark" class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <!-- Moon icon -->
        <svg x-show="!dark" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
      </button>
      <span class="font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->username }}</span>
      @if(auth()->user()->photo)
        <img src="{{ asset(auth()->user()->photo) }}" alt="Profile" class="h-10 w-10 rounded-full border border-gray-300 object-cover">
      @else
        <div class="h-10 w-10 rounded-full border border-gray-300 bg-blue-400 flex items-center justify-center text-white font-bold text-sm">
          {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
        </div>
      @endif
    </div>
  </header>


    <div class="flex flex-1">

  <aside class="w-64 bg-white dark:bg-gray-800 shadow-md flex flex-col transition-colors duration-200">
  <nav class="p-4 text-gray-800 dark:text-gray-200">
    <ul>
      <!-- Dashboard -->
      <li>
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100 dark:hover:bg-gray-700">🏠 Dashboard</a>
      </li>

      <!-- Pengguna -->
      <li class="mt-2">
        <button onclick="toggleDropdown('pengguna')" class="flex justify-between items-center w-full px-4 py-2 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-50">
          👤 Pengguna
          <span>&#9662;</span>
        </button>
        <ul id="dropdown-pengguna" class="ml-4 mt-1 hidden">
          <li><a href="{{ route('account.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Admin</a></li>
          <li><a href="{{ route('user.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Umum</a></li>
        </ul>
      </li>

      <!-- Edit Fitur -->
      <li class="mt-2">
        <button onclick="toggleDropdown('fitur')" class="flex justify-between items-center w-full px-4 py-2 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-50">
          ⚙️ Edit Fitur
          <span>&#9662;</span>
        </button>
        <ul id="dropdown-fitur" class="ml-4 mt-1 hidden">
          <li><a href="{{ route('event.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Jadwal</a></li>
          <li><a href="{{ route('news.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Berita</a></li>
          <li><a href="{{ route('content.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Tempat Wisata</a></li>
        </ul>
      </li>

      <!-- Pengajuan -->
      <li class="mt-2">
        <button onclick="toggleDropdown('pengajuan')" class="flex justify-between items-center w-full px-4 py-2 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-50">
          📁 Pengajuan
          <span>&#9662;</span>
        </button>
        <ul id="dropdown-pengajuan" class="ml-4 mt-1 hidden">
          <li><a href="{{ route('submission.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">List Pengajuan</a></li>
          <li><a href="{{ route('submission.approved.list') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Approved</a></li>
          <li><a href="{{ route('submission.rejected.list') }}" class="block px-4 py-1 rounded hover:bg-blue-100 dark:hover:bg-slate-700 text-gray-800 dark:text-slate-200">Rejected</a></li>
        </ul>
      </li>

      <!-- SPK -->
      <li class="mt-2">
        <a href="{{ route('admin.recommendation.simulation') }}" class="block px-4 py-2 rounded hover:bg-blue-100 dark:hover:bg-gray-700">🔍 Simulasi Rekomendasi</a>
      </li>
    </ul>
  </nav>
    <div class="px-4 py-3 border-t dark:border-slate-700 mt-auto">
      <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="button" id="btn-logout"
                class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 mt-1">
          Logout
        </button>
      </form>
    </div>
  </aside>


      <main class="flex-1 p-6">
        @yield('content')
      </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-800 p-4 text-center text-sm text-gray-500 dark:text-slate-400 transition-colors duration-200">
      © BLUD Pariwisata, All rights reserved.
    </footer>
  </div>

    <script>
      const dropdowns = ['pengguna', 'fitur', 'pengajuan'];

      function toggleDropdown(id) {
        dropdowns.forEach(name => {
          const el = document.getElementById('dropdown-' + name);
          if (name === id) {
            el.classList.toggle('hidden');
          } else {
            el.classList.add('hidden');
          }
        });
      }

  // Tutup dropdown jika klik di luar
        document.addEventListener('click', function (e) {
          const isInside = dropdowns.some(name => {
            return e.target.closest(`#dropdown-${name}`) || e.target.closest(`button[onclick*="${name}"]`);
          });

          if (!isInside) {
            dropdowns.forEach(name => {
              document.getElementById('dropdown-' + name).classList.add('hidden');
            });
          }
        });
      </script>

      <script>
        document.getElementById('btn-logout').addEventListener('click', function () {
          Swal.fire({
            title: 'Keluar dari sesi?',
            text: 'Anda akan keluar dari dashboard.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            confirmButtonColor: '#ef4444' // opsional
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('logout-form').submit();
            }
          });
        });
      </script>

</html>