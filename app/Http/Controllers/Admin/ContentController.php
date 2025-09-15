<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\ContentFeature;

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

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name'          => 'required|string|max:255',
                'description'   => 'nullable|string',
                'price_weekday' => 'nullable|string',
                'price_weekend' => 'nullable|string',
                'open_time'     => 'nullable|date_format:H:i',
                'close_time'    => 'nullable|date_format:H:i',
                'location'      => 'nullable|string|max:255',
                'location_embed'=> 'nullable|string',
                'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
                'instagram' => 'nullable|string',
                'tiktok' => 'nullable|string',
            ]);

            $existing = Content::where('name', $data['name'])->first();
            if ($existing) {
                return back()->with('error', 'Nama '. $data['name'] . ' sudah digunakan.');
            }

            $data['slug'] = Str::slug($data['name'], '-');

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('assets/content', 'public_html_storage');
            }

            $content = Content::create($data);

            Activity::create([
                'admin_id'    => auth('admin')->id(),
                'description' => 'menambahkan tempat wisata baru.',
            ]);

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

    public function update(Request $request, Content $content)
    {
        try {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:content,slug,' . $content->id,
            'description' => 'nullable|string',
            'price_weekday' => 'nullable|string',
            'price_weekend' => 'nullable|string',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'location_embed'=> 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
            'instagram' => 'nullable|string',
            'whatsapp' => 'nullable|string',
        ]);

        if (isset($data['name']) && $data['name'] !== $content->name) {
            $existing = Content::where('name', $data['name'])->first();
            if ($existing) {
                return back()->with('error', 'Nama ' . $data['name'] . ' sudah digunakan.');
            }
        }

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

        return redirect()->route('content.index')->with('success', 'Data berhasil dihapus.');
    }
}
