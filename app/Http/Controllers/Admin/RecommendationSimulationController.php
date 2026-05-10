<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RecommendationService;

class RecommendationSimulationController extends Controller
{
    /**
     * Menampilkan halaman form simulasi SPK.
     */
    public function index()
    {
        return view('admin.recommendation.simulation');
    }

    /**
     * Memproses data simulasi dan memanggil RecommendationService.
     */
    public function simulate(Request $request, RecommendationService $service)
    {
        $request->validate([
            'event_type'   => 'nullable|string|max:255',
            'participants' => 'nullable|integer|min:1',
            'date'         => 'required|date',
            'budget'       => 'nullable|numeric|min:0',
            'facilities'   => 'nullable|string',
            'preference'   => 'nullable|string|max:255',
        ]);

        // Konversi string fasilitas (koma) ke array
        $facilitiesArray = [];
        if ($request->filled('facilities')) {
            $facilitiesArray = array_map('trim', explode(',', $request->facilities));
            $facilitiesArray = array_filter($facilitiesArray); // hapus string kosong
        }

        $criteria = [
            'event_type'   => $request->event_type,
            'participants' => $request->participants,
            'date'         => $request->date,
            'budget'       => $request->budget,
            'facilities'   => $facilitiesArray,
            'preference'   => $request->preference,
        ];

        $results = $service->recommend($criteria);

        return view('admin.recommendation.simulation', compact('results', 'criteria'));
    }
}
