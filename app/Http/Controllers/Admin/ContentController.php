<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\ContentFeature;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Content::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $contents = $query->orderBy('id', 'desc')->get();

        return view('admin.content.index', compact('contents'));
    }

    public function create()
    {
        return view('admin.content.create');
    }

    public function store(StoreContentRequest $request)
    {
        try {
            $data = $request->validated();

            $data['slug'] = Str::slug($data['name'], '-');

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('assets/content', 'public_html_storage');
            }

            $content = Content::create($data);

            Activity::create([
                'admin_id'    => auth('admin')->id(),
                'description' => 'menambahkan tempat wisata baru.',
            ]);

            \Illuminate\Support\Facades\Cache::forget('wisata_all');

            return redirect()->route('content.facilities', ['id' => $content->id])
            ->with('success', 'Konten berhasil ditambahkan. Silakan tambahkan data fasilitas.');


        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan konten: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $content = Content::with('features')->findOrFail($id);
        return view('admin.content.edit', compact('content'));
    }

    public function update(UpdateContentRequest $request, Content $content)
    {
        try {
        $data = $request->validated();

        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name'], '-');
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $file->store('assets/content', 'public_html_storage'); // simpan ke storage/app/public/content
            $data['image'] = $imagePath;
        }

        $content->update($data);
        Activity::create([
            'admin_id' => auth('admin')->id(),
            'description' => 'mengedit tempat wisata.',
        ]);

        \Illuminate\Support\Facades\Cache::forget('wisata_all');

        return redirect()->route('content.index')->with('success', 'Konten berhasil diupdate.');
        } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan saat mengubah konten: ' . $e->getMessage());
        }
    }

    public function destroy(Content $content)
    {
        $content->delete();

        \Illuminate\Support\Facades\Cache::forget('wisata_all');

        return redirect()->route('content.index')->with('success', 'Data berhasil dihapus.');
    }
}
