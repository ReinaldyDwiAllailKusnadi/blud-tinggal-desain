
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="{{ asset('assets/js/index.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/input.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/output.css') }}"> 
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/svg/logo.svg') }}">

</head>
<body class="bg-gray-100">
  <div class="flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
    <div class="flex items-center gap-2">
      <img src="{{ asset('assets/img/logo blud.png') }}" alt="BLUD" class="h-10">
      <img src="{{ asset('assets/img/Telu.png') }}" alt="Telkom" class="h-12">
    </div>

    <div class="flex items-center">
      <span class="mr-4 font-medium text-gray-700">{{ auth()->user()->username }}</span>
      <img src="{{ auth()->user()->photo }}" alt="Profile" class="h-10 w-10 rounded-full border border-gray-300">
    </div>
  </header>


    <div class="flex flex-1">

  <aside class="w-64 bg-white shadow-md flex flex-col">
  <nav class="p-4">
    <ul>
      <!-- Dashboard -->
      <li>
        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100 text-gray-800">🏠 Dashboard</a>
      </li>

      <!-- Pengguna -->
      <li class="mt-2">
        <button onclick="toggleDropdown('pengguna')" class="flex justify-between items-center w-full px-4 py-2 rounded hover:bg-blue-100 text-gray-800">
          👤 Pengguna
          <span>&#9662;</span>
        </button>
        <ul id="dropdown-pengguna" class="ml-4 mt-1 hidden">
          <li><a href="{{ route('account.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Admin</a></li>
          <li><a href="{{ route('user.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Umum</a></li>
        </ul>
      </li>

      <!-- Edit Fitur -->
      <li class="mt-2">
        <button onclick="toggleDropdown('fitur')" class="flex justify-between items-center w-full px-4 py-2 rounded hover:bg-blue-100 text-gray-800">
          ⚙️ Edit Fitur
          <span>&#9662;</span>
        </button>
        <ul id="dropdown-fitur" class="ml-4 mt-1 hidden">
          <li><a href="{{ route('event.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Jadwal</a></li>
          <li><a href="{{ route('news.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Berita</a></li>
          <li><a href="{{ route('content.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Tempat Wisata</a></li>
        </ul>
      </li>

      <!-- Pengajuan -->
      <li class="mt-2">
        <button onclick="toggleDropdown('pengajuan')" class="flex justify-between items-center w-full px-4 py-2 rounded hover:bg-blue-100 text-gray-800">
          📁 Pengajuan
          <span>&#9662;</span>
        </button>
        <ul id="dropdown-pengajuan" class="ml-4 mt-1 hidden">
          <li><a href="{{ route('submission.index') }}" class="block px-4 py-1 rounded hover:bg-blue-100">List Pengajuan</a></li>
          <li><a href="{{ route('submission.approved.list') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Approved</a></li>
          <li><a href="{{ route('submission.rejected.list') }}" class="block px-4 py-1 rounded hover:bg-blue-100">Rejected</a></li>
        </ul>
      </li>
    </ul>
  </nav>
    <div class="px-4 py-3 border-t mt-auto">
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
    <footer class="bg-white p-4 text-center text-sm text-gray-500">
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