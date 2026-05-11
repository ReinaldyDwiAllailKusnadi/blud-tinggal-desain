<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubmissionApiController extends Controller
{
    /**
     * Buat pengajuan sewa baru (multipart upload)
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'namePIC' => 'required|string|max:100',
                'no_hp' => ['required', 'string', 'regex:/^[0-9]{10,12}$/'],
                'address' => 'required|string|max:255',
                'vendor' => 'required|string|max:100',
                'location' => 'required|exists:content,name',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'name_event' => 'required|string|max:255',
                'file' => 'required|file|mimes:pdf|max:2048',
                'ktp' => 'required|file|mimes:pdf|max:2048',
                'appl_letter' => 'required|file|mimes:pdf|max:2048',
                'actv_letter' => 'required|file|mimes:pdf|max:2048',
            ];

            $messages = [
                'no_hp.required' => 'Nomor HP wajib diisi.',
                'no_hp.regex' => 'Nomor HP harus berupa angka dengan panjang 10 sampai 12 digit.',
                'file.required' => 'File proposal wajib diunggah.',
                'file.mimes' => 'File proposal harus berformat PDF.',
                'file.max' => 'File proposal maksimal 2MB.',
                'ktp.required' => 'File KTP wajib diunggah.',
                'ktp.mimes' => 'File KTP harus berformat PDF.',
                'ktp.max' => 'File KTP maksimal 2MB.',
                'appl_letter.required' => 'Surat pengajuan wajib diunggah.',
                'appl_letter.mimes' => 'Surat pengajuan harus berformat PDF.',
                'appl_letter.max' => 'Surat pengajuan maksimal 2MB.',
                'actv_letter.required' => 'Surat kegiatan wajib diunggah.',
                'actv_letter.mimes' => 'Surat kegiatan harus berformat PDF.',
                'actv_letter.max' => 'Surat kegiatan maksimal 2MB.',
                'location.exists' => 'Lokasi yang dipilih tidak valid.',
            ];

            $data = $request->validate($rules, $messages);

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
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Submission API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.',
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

    /**
     * Download lampiran pengajuan (Mobile API)
     */
    public function download(Request $request, $id, $type)
    {
        try {
            $submission = Submission::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$submission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke lampiran ini.'
                ], 403);
            }

            $mapping = [
                'proposal'    => 'file',
                'ktp'         => 'ktp',
                'appl_letter' => 'appl_letter',
                'actv_letter' => 'actv_letter',
            ];

            if (!isset($mapping[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe file tidak valid.'
                ], 400);
            }

            $field = $mapping[$type];
            $path = $submission->$field;

            if (!$path || !\Storage::disk('public_html_storage')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lampiran tidak ditemukan.'
                ], 404);
            }

            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $filename = "{$type}-{$submission->id}.{$extension}";

            if ($type === 'appl_letter') $filename = "surat-pengajuan-{$submission->id}.{$extension}";
            if ($type === 'actv_letter') $filename = "surat-kegiatan-{$submission->id}.{$extension}";

            return \Storage::disk('public_html_storage')->download($path, $filename);
        } catch (\Exception $e) {
            Log::error('Download API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lampiran gagal diunduh.'
            ], 500);
        }
    }
}
