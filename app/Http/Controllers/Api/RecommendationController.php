<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Validator;

class RecommendationController extends Controller
{
    /**
     * Endpoint untuk mendapatkan rekomendasi lokasi berdasarkan kriteria user.
     * Menggunakan Knowledge-Based Recommendation (Similarity).
     */
    public function recommend(Request $request, RecommendationService $service)
    {
        $validator = Validator::make($request->all(), [
            'event_type'   => 'nullable|string|max:255',
            'participants' => 'nullable|integer|min:1',
            'date'         => 'required|date',
            'budget'       => 'nullable|numeric|min:0',
            'facilities'   => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'preference'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $recommendations = $service->recommend($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Rekomendasi lokasi berhasil dibuat',
                'data'    => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung rekomendasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
