<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Content;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // ✅ Homepage
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toDateString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];

        // ✅ Halaman statis
        $staticPages = ['/wisata', '/penyewaan', '/event'];
        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => url($page),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        // ✅ Halaman dinamis dari Content (wisata)
        $contents = Content::latest()->get();
        foreach ($contents as $item) {
            // halaman utama wisata
            $urls[] = [
                'loc' => url('/wisata/' . $item->slug),
                'lastmod' => $item->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ];
            // halaman booking
            $urls[] = [
                'loc' => url('/booking/' . $item->slug),
                'lastmod' => $item->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
            // halaman fasilitas
            $urls[] = [
                'loc' => url('/fasilitas/' . $item->slug),
                'lastmod' => $item->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        // 🔧 Generate XML dari view
        $xml = view('sitemap', compact('urls'));

        return response($xml, 200)
                ->header('Content-Type', 'application/xml');
    }
}
