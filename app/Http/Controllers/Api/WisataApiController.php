<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentFeature;

class WisataApiController extends Controller
{
    /**
     * Daftar semua wisata
     */
    public function index()
    {
        $contents = Content::paginate(10);
        
        $contents->getCollection()->transform(function ($content) {
            $content->image_url = $content->image ? url($content->image) : null;
            return $content;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data wisata berhasil diambil.',
            'data' => $contents->items(),
            'pagination' => [
                'total' => $contents->total(),
                'per_page' => $contents->perPage(),
                'current_page' => $contents->currentPage(),
                'last_page' => $contents->lastPage(),
                'next_page_url' => $contents->nextPageUrl(),
                'prev_page_url' => $contents->previousPageUrl(),
            ]
        ]);
    }

    /**
     * Detail wisata berdasarkan slug
     */
    public function show($slug)
    {
        $content = Content::where('slug', $slug)->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Wisata tidak ditemukan.',
            ], 404);
        }

        $content->image_url = $content->image ? url($content->image) : null;

        return response()->json([
            'success' => true,
            'message' => 'Detail wisata berhasil diambil.',
            'data' => $content,
        ]);
    }

    /**
     * Fasilitas dan harga sewa berdasarkan slug content
     */
    public function facilities($slug)
    {
        $content = Content::where('slug', $slug)->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Wisata tidak ditemukan.',
            ], 404);
        }

        $facilities = ContentFeature::where('location', $content->id)->get();

        $prices = $facilities->where('type', 'price')->values();
        $facilityItems = $facilities->where('type', 'facility')->values();

        return response()->json([
            'success' => true,
            'message' => 'Data fasilitas berhasil diambil.',
            'data' => [
                'content' => $content,
                'prices' => $prices,
                'facilities' => $facilityItems,
            ],
        ]);
    }
}
