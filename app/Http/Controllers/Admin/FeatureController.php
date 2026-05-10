<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pricelists;
use App\Models\Content;
use App\Models\ContentFeature;

class FeatureController extends Controller
{
    public function index($id)
    {
        $content = Content::with('features')->findOrFail($id);
        return view('admin.content.facilities', compact('content'));
    }

    public function store(Request $request)
    {
        $features = $request->input('features', []);
        if (is_array($features)) {
            foreach ($features as $key => $feature) {
                if (isset($feature['price'])) {
                    $features[$key]['price'] = preg_replace('/[^0-9]/', '', $feature['price']);
                }
            }
            $request->merge(['features' => $features]);
        }

        $validated = $request->validate([
            'location' => ['required', 'exists:content,id'],
            'features' => ['required', 'array', 'min:1'],
            'features.*.type' => ['required', 'in:price,facility'],
            'features.*.bagian' => ['nullable', 'string', 'max:255'],
            'features.*.luas' => ['nullable', 'string', 'max:255'],
            'features.*.price' => ['nullable', 'integer', 'min:0'],
            'features.*.facility_name' => ['nullable', 'string', 'max:255'],
        ]);

        $content = Content::findOrFail($validated['location']);
        $created = [];

        try {
            foreach ($validated['features'] as $feature) {
                if ($feature['type'] === 'facility' && empty($feature['facility_name'])) {
                    return back()->withInput()->withErrors(['features' => 'Nama fasilitas tidak boleh kosong.']);
                }

                if ($feature['type'] === 'price' && (empty($feature['bagian']) || empty($feature['luas']) || empty($feature['price']))) {
                    return back()->withInput()->withErrors(['features' => 'Data penyewaan (bagian, luas, harga) wajib diisi.']);
                }

                $created[] = $content->features()->create([
                    'type' => $feature['type'],
                    'bagian' => $feature['bagian'] ?? null,
                    'luas' => $feature['luas'] ?? null,
                    'price' => $feature['price'] ?? null,
                    'facility_name' => $feature['facility_name'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            foreach ($created as $row) {
                $row->delete();
            }
            throw $e;
        }

        return redirect()->route('content.index')->with('success', 'Fitur berhasil disimpan.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'location'           => ['required', 'exists:content,id'],
            'facility_names'     => ['nullable', 'array'],
            'facility_names.*'   => ['required_with:facility_names', 'string', 'max:255'],
            'features'           => ['nullable', 'array'],
            'features.*.bagian'  => ['nullable', 'string'],
            'features.*.luas'    => ['nullable', 'string'],
            'features.*.price'   => ['nullable', 'integer'],
        ]);

        $location = $validated['location'];

        $features = $validated['features'] ?? [];
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
        $facilityNames = $validated['facility_names'] ?? [];
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

        return redirect()->route('content.index')->with('success', 'Data berhasil diperbarui.');
    }

}
