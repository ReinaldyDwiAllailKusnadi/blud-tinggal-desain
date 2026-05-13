<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Masuk - BLUD Pariwisata</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/svg/logo.svg') }}">

</head>
<body class="min-h-screen bg-cover bg-center bg-no-repeat flex items-center justify-center px-4" style="background-image: url('{{ asset('assets/img/menara.jpg') }}');">

  <div class="bg-white/90 backdrop-blur-md p-6 md:p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Masuk Akun</h2>

    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5 text-sm">
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-4">
      @csrf
      <div>
        <label class="block text-sm text-gray-600 mb-1" for="email">Email</label>
        <input type="email" id="email" name="email" required
          class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <div>
        <label class="block text-sm text-gray-600 mb-1" for="password">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        <div class="flex justify-end mt-1">
          <a href="{{ route('forgot.password.form') }}" class="text-xs text-blue-500 font-semibold hover:underline">
            Lupa Password?
          </a>
        </div>
      </div>

      <button type="submit"
        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
        Masuk
      </button>
    </form>

    <div class="my-6 relative">
    <div class="absolute inset-0 flex items-center">
    <div class="w-full border-t border-gray-300"></div>
    </div>
    <div class="relative flex justify-center text-sm">
    <span class="bg-white/90 px-2 text-gray-500">atau masuk dengan</span>
    </div>
    </div>

    <a href="{{ route('google.login') }}"
       class="flex items-center justify-center gap-3 border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 hover:shadow-md font-semibold py-2 px-4 rounded-lg transition duration-200">
    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
    <span class="text-sm">Masuk dengan Google</span>
    </a>

    <p class="text-sm text-center text-gray-600 mt-4">
        Tidak punya akun? <a href="{{ route('register') }}" class="text-blue-500 font-semibold hover:underline">Daftar</a>
    </p>

</body>
</html>
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
