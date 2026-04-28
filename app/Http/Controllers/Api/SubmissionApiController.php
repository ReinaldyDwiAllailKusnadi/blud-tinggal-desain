<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Submission;
use Carbon\Carbon;

class SubmissionApiController extends Controller
{
    /**
     * Buat pengajuan sewa baru (multipart upload)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'namePIC' => 'required|string|max:100',
                'no_hp' => 'required|string|max:12',
                'address' => 'required|string|max:255',
                'vendor' => 'required|string|max:100',
                'location' => 'required|exists:content,name',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'name_event' => 'required|string|max:255',
                'file' => 'file|mimes:pdf|max:5048',
                'ktp' => 'required|file|mimes:pdf|max:5048',
                'appl_letter' => 'file|mimes:pdf|max:5048',
                'actv_letter' => 'file|mimes:pdf|max:5048',
            ]);

            $data['user_id'] = $request->user()->id;
            $data['status'] = 'pending';
            $data['apply_date'] = Carbon::now()->format('Y-m-d H:i');

            // Handle file uploads
            if ($request->hasFile('file')) {
                $data['file'] = $request->file('file')->store('assets/rundowns', 'public_html_storage');
            }
            if ($request->hasFile('ktp')) {
                $data['ktp'] = $request->file('ktp')->store('assets/ktp', 'public_html_storage');
            }
            if ($request->hasFile('appl_letter')) {
                $data['appl_letter'] = $request->file('appl_letter')->store('assets/appl_letters', 'public_html_storage');
            }
            if ($request->hasFile('actv_letter')) {
                $data['actv_letter'] = $request->file('actv_letter')->store('assets/actv_letters', 'public_html_storage');
            }

            $data['notes'] = $request->input('notes', null);

            $submission = Submission::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim. Harap tunggu informasi lebih lanjut.',
                'data' => $submission,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Riwayat pengajuan milik user yang login
     */
    public function history(Request $request)
    {
        $submissions = Submission::where('user_id', $request->user()->id)
            ->orderBy('apply_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                // Konversi path file ke full URL
                $item->file_url = $item->file ? url('storage/' . $item->file) : null;
                $item->ktp_url = $item->ktp ? url('storage/' . $item->ktp) : null;
                $item->appl_letter_url = $item->appl_letter ? url('storage/' . $item->appl_letter) : null;
                $item->actv_letter_url = $item->actv_letter ? url('storage/' . $item->actv_letter) : null;
                return $item;
            });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengajuan berhasil diambil.',
            'data' => $submissions,
        ]);
    }

    /**
     * Daftar lokasi untuk dropdown di form pengajuan
     */
    public function locations()
    {
        $contents = Content::select('id', 'name', 'slug')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar lokasi berhasil diambil.',
            'data' => $contents,
        ]);
    }
}
