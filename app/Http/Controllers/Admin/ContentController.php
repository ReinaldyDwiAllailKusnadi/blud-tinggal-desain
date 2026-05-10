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
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Content::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['id', 'name', 'price_weekday', 'location', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'asc';

        $contents = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());

        return view('admin.content.index', compact('contents', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        return view('admin.content.create');
    }

    public function store(StoreContentRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Debug logging
            \Log::info('Content store validated data', $data);

            $data['is_indoor'] = $request->boolean('is_indoor');
            $data['is_outdoor'] = $request->boolean('is_outdoor');

            $data['slug'] = Str::slug($data['name'], '-');
            $data['whatsapp'] = $request->input('whatsapp');

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
            
            // Debug logging
            \Log::info('Content update request whatsapp', [
                'request_whatsapp' => $request->input('whatsapp'),
                'validated' => $request->validated(),
            ]);
            \Log::info('Content update validated data', $data);

            $data['is_indoor'] = $request->boolean('is_indoor');
            $data['is_outdoor'] = $request->boolean('is_outdoor');

            $oldImage = $content->image;

            if (!empty($data['name'])) {
                $data['slug'] = Str::slug($data['name'], '-');
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imagePath = $file->store('assets/content', 'public_html_storage'); // simpan ke storage/app/public/assets/content
                $data['image'] = $imagePath;
            }

            $data['whatsapp'] = $request->input('whatsapp');

            $content->update($data);

            // Update Fasilitas & Harga Area
            $this->syncContentFeatures($content, $request);

            // Bersihkan gambar lama jika ada upload baru dan bukan gambar seed
            if ($request->hasFile('image') && $oldImage) {
                if (str_starts_with($oldImage, 'assets/content/')) {
                    if (Storage::disk('public_html_storage')->exists($oldImage)) {
                        Storage::disk('public_html_storage')->delete($oldImage);
                    }
                }
            }

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
        $oldImage = $content->image;
        
        $content->delete();

        // Bersihkan gambar jika itu adalah file upload (assets/content/)
        if ($oldImage && str_starts_with($oldImage, 'assets/content/')) {
            if (Storage::disk('public_html_storage')->exists($oldImage)) {
                Storage::disk('public_html_storage')->delete($oldImage);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('wisata_all');

        return redirect()->route('content.index')->with('success', 'Data berhasil dihapus.');
    }

    private function syncContentFeatures(Content $content, Request $request)
    {
        $location = $content->id;

        // ================= Harga Area =================
        $features = $request->input('features', []);
        $submittedBagian = [];

        $existingPrices = ContentFeature::where('location', $location)
            ->where('type', 'price')
            ->get()
            ->keyBy('bagian');

        foreach ($features as $feature) {
            $bagian = $feature['bagian'] ?? null;
            if (!$bagian) continue;

            $submittedBagian[] = $bagian;

            $data = [
                'bagian' => $bagian,
                'luas'   => $feature['luas'] ?? null,
                'price'  => $feature['price'] ?? null,
            ];

            if (isset($existingPrices[$bagian])) {
                $existingPrices[$bagian]->update($data);
            } else {
                ContentFeature::create(array_merge($data, [
                    'location' => $location,
                    'type'     => 'price',
                ]));
            }
        }

        ContentFeature::where('location', $location)
            ->where('type', 'price')
            ->whereNotIn('bagian', $submittedBagian)
            ->delete();

        // ================= Fasilitas =================
        $facilityNames = $request->input('facility_names', []);
        $submittedNames = [];

        $existingFacilities = ContentFeature::where('location', $location)
            ->where('type', 'facility')
            ->pluck('id', 'facility_name');

        foreach ($facilityNames as $name) {
            if (!$name) continue;

            $submittedNames[] = $name;

            if (!isset($existingFacilities[$name])) {
                ContentFeature::create([
                    'location'      => $location,
                    'type'          => 'facility',
                    'facility_name' => $name,
                ]);
            }
        }

        ContentFeature::where('location', $location)
            ->where('type', 'facility')
            ->whereNotIn('facility_name', $submittedNames)
            ->delete();
    }
}
