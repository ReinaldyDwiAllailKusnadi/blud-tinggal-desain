<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\News;

class HomeApiController extends Controller
{
    /**
     * Data beranda: contents + news
     */
    public function index()
    {
        $contents = Content::all()->map(function ($content) {
            $content->image_url = $content->image ? url($content->image) : null;
            return $content;
        });

        $news = News::orderBy('upload_time', 'desc')->get()->map(function ($item) {
            $item->image_url = $item->image ? url($item->image) : null;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data beranda berhasil diambil.',
            'data' => [
                'contents' => $contents,
                'news' => $news,
            ],
        ]);
    }
}
