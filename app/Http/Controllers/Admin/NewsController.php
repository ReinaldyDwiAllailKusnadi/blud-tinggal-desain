<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\News;
use Carbon\Carbon; 
use App\Models\Activity;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['id', 'title', 'upload_time', 'source'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $news = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());

        return view('admin.news.index', compact('news', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'source' => 'required|string',
        ]);

        $data['upload_time'] = Carbon::now()->format('Y-m-d H:i');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $file->store('assets/news', 'public_html_storage'); // simpan ke storage/app/public/news
            $data['image'] = $imagePath;
        }

        News::create($data);
        Activity::create([
            'admin_id' => auth('admin')->id(),
            'description' => 'menambahkan berita baru.',
        ]);

        return redirect()->route('news.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'source' => 'required|string',
        ]);

        $data['upload_time'] = Carbon::now()->format('Y-m-d H:i');

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($news->image) {
                Storage::disk('public_html_storage')->delete($news->image);
            }
            $file = $request->file('image');
            $data['image'] = $file->store('assets/news', 'public_html_storage');
        }

        $news->update($data);
        Activity::create([
            'admin_id' => auth('admin')->id(),
            'description' => 'mengedit berita.',
        ]);

        return redirect()->route('news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Hapus gambar jika ada
        if ($news->image) {
            Storage::disk('public_html_storage')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('news.index')->with('success', 'Berita berhasil dihapus.');
    }


}
