<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Event;
use App\Models\Submission;
use Carbon\Carbon;

class BookingApiController extends Controller
{
    /**
     * Daftar semua lokasi untuk jadwal (sama dengan daftar content)
     */
    public function locations()
    {
        $contents = Content::all()->map(function ($content) {
            $content->image_url = $content->image ? url($content->image) : null;
            return $content;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data lokasi jadwal berhasil diambil.',
            'data' => $contents,
        ]);
    }

    /**
     * Jadwal booking per lokasi (grouped by bulan)
     */
    public function byLocation($slug)
    {
        $content = Content::where('slug', $slug)->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan.',
            ], 404);
        }

        // Ambil events
        $events = Event::select('name_event', 'start_date', 'end_date', 'location')
            ->where('location', $content->name)
            ->get()
            ->map(function ($item) {
                $item->type = 'event';
                return $item;
            });

        // Ambil submissions yang approved
        $submissions = Submission::select('name_event', 'start_date', 'end_date', 'location')
            ->where('status', 'approved')
            ->where('location', $content->name)
            ->get()
            ->map(function ($item) {
                $item->type = 'submission';
                return $item;
            });

        // Gabungkan dan group per bulan
        $combined = $events->concat($submissions)->sortBy('start_date');

        $grouped = $combined->groupBy(function ($item) {
            return Carbon::parse($item->start_date)->translatedFormat('F');
        });

        // Format untuk API
        $result = [];
        foreach ($grouped as $bulan => $items) {
            $result[] = [
                'bulan' => $bulan,
                'jumlah_event' => $items->count(),
                'events' => $items->values(),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal booking berhasil diambil.',
            'data' => [
                'content' => $content,
                'jadwal' => $result,
            ],
        ]);
    }

    /**
     * Detail jadwal per bulan
     */
    public function byMonth($slug, $bulan)
    {
        $bulanMap = [
            'januari' => 'january',
            'februari' => 'february',
            'maret' => 'march',
            'april' => 'april',
            'mei' => 'may',
            'juni' => 'june',
            'juli' => 'july',
            'agustus' => 'august',
            'september' => 'september',
            'oktober' => 'october',
            'november' => 'november',
            'desember' => 'december',
        ];

        $bulanEn = $bulanMap[strtolower($bulan)] ?? $bulan;
        $monthNumber = Carbon::parse("1 $bulanEn")->month;

        $content = Content::where('slug', $slug)->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan.',
            ], 404);
        }

        $events = Event::where('location', $content->name)
            ->whereMonth('start_date', $monthNumber)
            ->orderBy('start_date')
            ->get()
            ->map(function ($event) {
                $event->pdf_url = $event->file ? url('storage/' . $event->file) : null;
                $event->type = 'event';
                return $event;
            });

        $submissions = Submission::where('status', 'approved')
            ->where('location', $content->name)
            ->whereMonth('start_date', $monthNumber)
            ->orderBy('start_date')
            ->get()
            ->map(function ($submission) {
                $submission->pdf_url = $submission->actv_letter ? url('storage/' . $submission->actv_letter) : null;
                $submission->type = 'submission';
                return $submission;
            });

        $merged = $events->merge($submissions)->sortBy('start_date')->values();

        return response()->json([
            'success' => true,
            'message' => 'Detail jadwal bulan berhasil diambil.',
            'data' => [
                'content' => $content,
                'bulan' => $bulan,
                'events' => $merged,
            ],
        ]);
    }
}
