<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('sitemap.xml') }}">
  <link rel="canonical" href="https://bludpariwisata.com{{ request()->getPathInfo() }}">

  {{-- CSS --}}
  <link rel="stylesheet" href="{{ asset('assets/css/input.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/output.css') }}" />

  {{-- JS & Libraries --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="{{ asset('assets/js/index.js') }}"></script>

  {{-- Favicon --}}
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/svg/logo.svg') }}">

  {{-- Default Meta --}}
  <meta name="keywords" content="BLUD, Pariwisata, Baturraden, Banyumas">
  <meta name="author" content="BLUD Team">
  <link rel="canonical" href="@yield('canonical', url('/'))">

  {{-- SEO Meta --}}
  <title>@yield('title', 'BLUD Pariwisata Banyumas | Wisata & Layanan Resmi')</title>
  <meta name="description" content="@yield('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')">
  <meta name="keywords" content="BLUD Pariwisata, Pariwisata, Baturraden, Banyumas, Wisata, Tiket, Event">
  <meta name="robots" content="index, follow">

   {{-- Open Graph / Facebook --}}
  <meta property="og:title" content="@yield('title', 'BLUD Pariwisata Baturraden')">
  <meta property="og:description" content="@yield('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')">
  <meta property="og:image" content="{{ asset('assets/img/logo blud.png') }}">
  <meta property="og:url" content="@yield('canonical', url()->current())">
  <meta property="og:type" content="website">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('title', 'BLUD Pariwisata Baturraden')">
  <meta name="twitter:description" content="@yield('meta_description', 'Website resmi BLUD Pariwisata Baturraden. Informasi wisata, jadwal acara, dan booking online di Banyumas.')">
  <meta name="twitter:image" content="{{ asset('assets/img/logo blud.png') }}">

@php
    $seoSchema = [
        "@context" => "https://schema.org",
        "@type" => "LocalBusiness",
        "name" => "BLUD Pariwisata Baturraden",
        "image" => asset('assets/img/logo blud.png'),
        "url" => url('/'),
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => "Glempang, Bancarkembar, Purwokerto Utara",
            "addressLocality" => "Banyumas",
            "postalCode" => "53121",
            "addressCountry" => "ID"
        ],
        "telephone" => "+62-812-2828-9422",
        "sameAs" => [
            "https://instagram.com/bludpariwisata",
            "https://wa.me/6281228289422"
        ]
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($seoSchema, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>

</head>

<body class="min-h-screen flex flex-col pt-20">
  {{-- Header --}}
  <header class="bg-white fixed top-0 left-0 right-0 z-20 shadow-sm">
    <nav class="w-full max-w-7xl mx-auto px-5 py-2 flex items-center justify-between">
      
      {{-- Logo --}}
      <div>
        <img width="100" src="{{ asset('assets/img/logo blud.png') }}" alt="BLUD"/>
      </div>

      {{-- Hamburger & Menu --}}
      <div class="relative">
        {{-- Hamburger Button --}}
        <button id="hamburger" name="hamburger" type="button" class="block lg:hidden">
          <span class="hamburger-line origin-top-left transition duration-300 ease-in-out"></span>
          <span class="hamburger-line transition duration-300 ease-in-out"></span>
          <span class="hamburger-line origin-bottom-left transition duration-300 ease-in-out"></span>
        </button>

        {{-- Main Navigation --}}
        <div id="nav-menu"
             class="absolute right-4 top-full hidden w-full max-w-[250px] rounded-lg bg-white py-5 shadow-lg 
                    lg:static lg:block lg:max-w-full lg:rounded-none lg:bg-transparent lg:shadow-none">
          <ul class="block lg:flex lg:items-center">
            <li>
              <a href="{{ route('home') }}" class="mx-4 flex py-2 text-base {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-800' }} hover:text-primary">Home</a>
            </li>
            <li>
              <a href="{{ route('event') }}" class="mx-4 flex py-2 text-base {{ request()->routeIs('event') ? 'text-primary' : 'text-gray-800' }} hover:text-primary">Jadwal</a>
            </li>
            <li>
              <a href="{{ route('wisata') }}" class="mx-4 flex py-2 text-base {{ request()->routeIs('wisata') ? 'text-primary' : 'text-gray-800' }} hover:text-primary">Objek Wisata</a>
            </li>
            <li>
              <a href="{{ route('submission') }}" class="mx-4 flex py-2 text-base {{ request()->routeIs('submission') ? 'text-primary' : 'text-gray-800' }} hover:text-primary">Booking</a>
            </li>

            {{-- Guest --}}
            @guest
              <li>
                <a href="{{ route('login') }}" class="mx-4 flex items-center justify-center py-2 px-4 text-base font-bold text-blue-400 border border-blue-400 rounded-md hover:bg-blue-400 hover:text-white transition">
                  Masuk/Daftar
                </a>
              </li>
            @endguest

            {{-- Auth --}}
            @auth
              <li>
                <a href="{{ route('user.history') }}" class="mx-4 flex py-2 text-base {{ request()->routeIs('user.history') ? 'text-primary' : 'text-gray-800' }} hover:text-primary">Riwayat Pengajuan</a>
              </li>
              <li class="relative">
                <button id="userMenuButton" class="flex items-center gap-2 py-2 px-4 font-bold text-gray-800 hover:text-blue-500 focus:outline-none">
                  <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff"
                       alt="Profile" class="w-8 h-8 rounded-full border" />
                </button>
                <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-md py-2 z-50 hidden">
                  <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" id="btn-logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                      Logout
                    </button>
                  </form>
                </div>
              </li>
            @endauth
          </ul>
        </div>
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden"></div>

        {{-- Sidebar (Mobile) --}}
        <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 z-50">
          {{-- Header Sidebar --}}
          <div class="p-4 border-b flex justify-between items-center">
            <span class="text-lg font-bold text-blue-500">Menu</span>
            <button id="closeBtn" class="text-gray-700 text-2xl hover:text-red-500 transition">&times;</button>
          </div>

          {{-- Menu List --}}
          <ul class="p-4 space-y-2">
            <li>
              <a href="{{ route('home') }}" 
                class="block p-2 rounded {{ request()->routeIs('home') ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                Home
              </a>
            </li>
            <li>
              <a href="{{ route('wisata') }}" 
                class="block p-2 rounded {{ request()->routeIs('wisata') ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                Objek Wisata
              </a>
            </li>
            <li>
              <a href="{{ route('event') }}" 
                class="block p-2 rounded {{ request()->routeIs('event') ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                Jadwal
              </a>
            </li>
            <li>
              <a href="{{ route('submission') }}" 
                class="block p-2 rounded {{ request()->routeIs('submission') ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                Booking
              </a>
            </li>
            @guest
              <li>
                <a href="{{ route('login') }}" 
                  class="block text-center py-2 px-4 text-base font-bold text-blue-500 border border-blue-500 rounded-md hover:bg-blue-500 hover:text-white transition">
                  Masuk / Daftar
                </a>
              </li>
            @endguest
            @auth
              <li>
                <a href="{{ route('user.history') }}" 
                  class="block p-2 rounded {{ request()->routeIs('user.history') ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                  Riwayat Pengajuan
                </a>
              </li>
              <li>
                <a href="{{ route('profile') }}" 
                  class="block p-2 rounded {{ request()->routeIs('profile') ? 'bg-blue-100 text-blue-600 font-semibold' : 'hover:bg-gray-100' }}">
                  Profile
                </a>
              </li>
              <li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" 
                          class="w-full text-left p-2 text-red-600 hover:bg-gray-100 rounded">
                    Logout
                  </button>
                </form>
              </li>
            @endauth
          </ul>
        </div>
      </div>
    </nav>
  </header>

  {{-- Main Content --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer>
    <div class="bg-slate-100 py-4 px-8 lg:flex lg:justify-around">
      <div class="flex items-center space-x-4">
        <img src="{{ asset('assets/img/logo blud.png') }}" alt="BLUD" class="h-14 sm:h-20 object-contain">
        <img src="{{ asset('assets/img/Telu.png') }}" alt="Telkom" class="h-14 sm:h-20 object-contain">
      </div>
      <div class="text-primary py-4 flex justify-between flex-wrap gap-4 lg:gap-10">
        <div class="flex flex-col gap-2">
          <h2 class="font-bold text-xl">Link Terkait</h2>
          <a href="{{ route('home') }}" class="font-semibold text-base">Home</a>
          <a href="{{ route('wisata') }}" class="font-semibold text-base">Objek Wisata</a>
          <a href="{{ route('event') }}" class="font-semibold text-base">Jadwal</a>
        </div>
        <div class="flex flex-col gap-2">
          <h2 class="font-bold text-xl">Alamat</h2>
          <div class="flex items-start gap-2">
            <img src="{{ asset('assets/svg/location.svg') }}" alt="Location" />
            <a href="https://www.google.com/maps?q=Kantor+BLUD+Pariwisata+Baturraden+Glempang,+Bancarkembar,+Purwokerto+Utara,+Banyumas,+53121"
               target="_blank"
               class="font-semibold text-xs text-justify max-w-[150px] text-primary hover:underline">
              Kantor BLUD Pariwisata Baturraden Glempang, Bancarkembar,
              Purwokerto Utara, Banyumas, 53121
            </a>
          </div>
        </div>
        <div class="flex flex-col gap-2">
          <h2 class="font-bold text-xl">Social Media</h2>
          <div class="flex items-center gap-2">
            <img src="{{ asset('assets/svg/wa.svg') }}" alt="WhatsApp" />
            <a href="https://wa.me/6281228289422" target="_blank" class="font-semibold text-xs text-primary hover:underline">
              0812-2828-9422
            </a>
          </div>
          <div class="flex items-center gap-2">
            <img src="{{ asset('assets/svg/instagram.svg') }}" alt="Instagram" />
            <a href="https://instagram.com/bludpariwisata" target="_blank" class="font-semibold text-xs text-primary hover:underline">
              BLUD Pariwisata
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-primary p-4 text-center">
      <p class="text-white font-semibold">Copyright © BLUD Pariwisata, All rights reserved.</p>
    </div>
  </footer>

  @stack('scripts')

  {{-- Script --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const userMenuButton = document.getElementById('userMenuButton');
      const userDropdown = document.getElementById('userDropdown');

      if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function (e) {
          e.stopPropagation();
          userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
          if (!userDropdown.contains(event.target) && !userMenuButton.contains(event.target)) {
            userDropdown.classList.add('hidden');
          }
        });
      }

      const logoutBtn = document.getElementById('btn-logout');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
          Swal.fire({
            title: 'Keluar dari sesi?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            confirmButtonColor: '#ef4444'
          }).then((result) => {
            if (result.isConfirmed) {
              document.getElementById('logout-form').submit();
            }
          });
        });
      }
    });

  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("overlay");
  const closeBtn = document.getElementById("closeBtn");
  const hamburger = document.getElementById("hamburger");

  function openSidebar() {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
  }

  function closeSidebar() {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
  }

  hamburger?.addEventListener("click", openSidebar);
  closeBtn?.addEventListener("click", closeSidebar);
  overlay?.addEventListener("click", closeSidebar);

  </script>
</body>
</html>
