<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Buat Password Baru - BLUD Pariwisata</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/svg/logo.svg') }}">
</head>
<body class="min-h-screen bg-cover bg-center bg-no-repeat flex items-center justify-center px-4" style="background-image: url('{{ asset('assets/img/menara.jpg') }}');">

  <div class="bg-white/90 backdrop-blur-md p-6 md:p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Buat Password Baru</h2>
    <p class="text-sm text-center text-gray-600 mb-6">Masukkan password baru untuk akun Anda.</p>

    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5 text-sm">
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('forgot.password.update') }}" method="POST" class="flex flex-col gap-4">
      @csrf
      <div>
        <label class="block text-sm text-gray-600 mb-1" for="password">Password Baru</label>
        <input type="password" id="password" name="password" required
          class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <div>
        <label class="block text-sm text-gray-600 mb-1" for="password_confirmation">Konfirmasi Password Baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
          class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <button type="submit"
        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        Ubah Password
      </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:underline">Kembali ke Login</a>
    </div>
  </div>

  @if(session('success') || session('error'))
  <script>
      Swal.fire({
          title: "{{ session('success') ? 'Berhasil!' : 'Gagal!' }}",
          text: "{{ session('success') ?? session('error') }}",
          icon: "{{ session('success') ? 'success' : 'error' }}",
          confirmButtonColor: "{{ session('success') ? '#3085d6' : '#d33' }}"
      });
  </script>
  @endif

</body>
</html>
