@extends('layouts.sidebar')

@section('content')
<main class="p-6 bg-gray-50 flex-1">
    <div class="mb-6">
        <h3 class="text-2xl font-semibold mb-2">Edit Berita</h3>
    </div>

    <div class="bg-white p-5 rounded-lg shadow max-w-6xl mx-auto">
        <form action="{{ route('news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Upload Gambar --}}
            <div class="mb-4">
                <label class="font-medium block mb-1">Upload Gambar Baru (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="border px-4 py-2 rounded-lg w-full">
                @if($news->image)
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">Gambar Saat Ini:</p>
                        <img src="{{ asset($news->image) }}" alt="Gambar Berita" class="h-40 mt-1 rounded shadow">
                    </div>
                @endif
            </div>

            {{-- Judul --}}
            <div class="mb-4">
                <label for="title" class="font-medium block mb-1">Judul Berita</label>
                <input type="text" name="title" id="title" class="border px-4 py-2 rounded-lg w-full" value="{{ old('title', $news->title) }}" required>
            </div>

            {{-- Isi Berita --}}
            <div class="mb-4">
                <label for="content" class="font-medium block mb-1">Isi Berita</label>
                <textarea name="content" id="content" rows="6" class="border px-4 py-2 rounded-lg w-full" required>{{ old('content', $news->content) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="source" class="font-medium block mb-1">Sumber Berita</label>
                <input name="source" id="source" rows="6" class="border px-4 py-2 rounded-lg w-full" value="{{ old('source', $news->source) }}" required>
            </div>

            <div class="mt-6 flex justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">Update</button>
                <a href="{{ route('news.index') }}" class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded-md">Kembali</a>
            </div>

        </form>
    </div>
</main>

@if(session('error'))
<script>
    Swal.fire({
        title: "Gagal!",
        text: "{{ session('error') }}",
        icon: "error",
        confirmButtonColor: "#d33"
    });
</script>
@endif

@endsection
