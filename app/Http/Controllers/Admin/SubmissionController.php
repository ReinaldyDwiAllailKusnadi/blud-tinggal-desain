<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Content;
use Carbon\Carbon; 
use App\Mail\BookingStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::query()->where('status', 'pending');

        if ($request->filled('search')) {
            $query->where('vendor', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('location')) {
            $query->where('content_id', $request->location);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['id', 'vendor', 'name_event', 'created_at', 'location'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $submissions = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());
        $contents = Content::all();
        
        return view('admin.submission.index', compact('submissions', 'contents', 'sortBy', 'sortDir'));
    }

    public function exportPdf(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = Submission::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.submission.pdf', compact('submissions', 'status'));
        
        return $pdf->download('Laporan_Pengajuan_Sewa_' . \Carbon\Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function approved(Request $request)
    {
        $query = Submission::query()->where('status', 'approved');

        if ($request->filled('search')) {
            $query->where('vendor', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('location')) {
            $query->where('content_id', $request->location);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['id', 'vendor', 'name_event', 'created_at', 'location'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $submissions = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());
        $contents = Content::all();

        return view('admin.submission.approved', compact('submissions', 'contents', 'sortBy', 'sortDir'));
    }

    public function rejected(Request $request)
    {
        $query = Submission::query()->where('status', 'rejected');

        if ($request->filled('search')) {
            $query->where('vendor', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('location')) {
            $query->where('content_id', $request->location);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['id', 'vendor', 'name_event', 'created_at', 'location'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $submissions = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());
        $contents = Content::all();

        return view('admin.submission.rejected', compact('submissions', 'contents', 'sortBy', 'sortDir'));
    }

    public function approve($id)
    {
        $submission = Submission::findOrFail($id);
        $submission->status = 'approved';
        $submission->save();

        // Kirim Notifikasi (Email & Push)
        $this->sendStatusNotifications($submission, 'approved');

        return redirect()->back()->with('success', 'Pengajuan disetujui dan notifikasi berhasil dikirim.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $submission = Submission::findOrFail($id);
        $submission->status = 'rejected';
        $submission->notes = $request->notes;
        $submission->save();

        // Kirim Notifikasi (Email & Push)
        $this->sendStatusNotifications($submission, 'rejected');

        return redirect()->back()->with('success', 'Pengajuan ditolak dan notifikasi berhasil dikirim.');
    }

    /**
     * Helper untuk kirim Email & Push Notification
     */
    private function sendStatusNotifications($submission, $status)
    {
        $user = $submission->user;
        if (!$user) return;

        // 1. Kirim Email
        try {
            if ($user->email) {
                Mail::to($user->email)->send(new BookingStatusMail($submission, $status));
            }
        } catch (\Throwable $e) {
            Log::warning("Email notification failed for submission #{$submission->id}: " . $e->getMessage());
        }

        // 2. Kirim Push Notification (FCM)
        try {
            if ($user->fcm_token) {
                $title = $status === 'approved' ? 'Pengajuan Disetujui! ✅' : 'Pengajuan Ditolak ❌';
                $body = $status === 'approved' 
                    ? "Selamat! Pengajuan sewa untuk '{$submission->name_event}' telah disetujui."
                    : "Mohon maaf, pengajuan sewa untuk '{$submission->name_event}' ditolak. Cek aplikasi untuk detail.";

                app(\App\Services\NotificationService::class)->sendNotification(
                    $user->fcm_token,
                    $title,
                    $body,
                    ['submission_id' => $submission->id, 'status' => $status]
                );
            }
        } catch (\Throwable $e) {
            Log::warning("FCM notification failed for submission #{$submission->id}: " . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $submission = Submission::findOrFail($id);
        $contents = Content::all();
        return view('admin.event.submission', compact('submission', 'contents'));
    }

    public function update(Request $request, $id)
    {
        try {
            $submission = Submission::findOrFail($id);

            $data = $request->validate([
                'namePIC'    => 'required|string|max:100',
                'no_hp'      => 'required|string|max:12',
                'address'    => 'required|string|max:255',
                'vendor'     => 'required|string|max:100',
                'content_id' => 'required|exists:content,id',
                'start_date' => 'required|date',
                'end_date'   => 'required|date',
                'name_event' => 'required|string|max:255',
                'file'       => 'nullable|file|mimes:pdf|max:5048',
                'ktp'        => 'nullable|file|mimes:pdf|max:5048',
                'appl_letter'=> 'nullable|file|mimes:pdf|max:5048',
                'actv_letter'=> 'nullable|file|mimes:pdf|max:5048',
            ]);

            $content = Content::findOrFail($data['content_id']);
            $data['location'] = $content->name;

            // update status tidak diubah, tetap sesuai yg ada
            $data['notes'] = $request->input('notes', $submission->notes);

            // update file jika ada upload baru
            if ($request->hasFile('file')) {
                if ($submission->file && \Storage::disk('public_html_storage')->exists($submission->file)) {
                    \Storage::disk('public_html_storage')->delete($submission->file);
                }
                $filePath = $request->file('file')->store('assets/rundowns', 'public_html_storage');
                $data['file'] = $filePath;
            }

            if ($request->hasFile('ktp')) {
                if ($submission->ktp && \Storage::disk('public_html_storage')->exists($submission->ktp)) {
                    \Storage::disk('public_html_storage')->delete($submission->ktp);
                }
                $ktpPath = $request->file('ktp')->store('assets/ktp', 'public_html_storage');
                $data['ktp'] = $ktpPath;
            }

            if ($request->hasFile('appl_letter')) {
                if ($submission->appl_letter && \Storage::disk('public_html_storage')->exists($submission->appl_letter)) {
                    \Storage::disk('public_html_storage')->delete($submission->appl_letter);
                }
                $applLetterPath = $request->file('appl_letter')->store('assets/appl_letters', 'public_html_storage');
                $data['appl_letter'] = $applLetterPath;
            }

            if ($request->hasFile('actv_letter')) {
                if ($submission->actv_letter && \Storage::disk('public_html_storage')->exists($submission->actv_letter)) {
                    \Storage::disk('public_html_storage')->delete($submission->actv_letter);
                }
                $actvLetterPath = $request->file('actv_letter')->store('assets/actv_letters', 'public_html_storage');
                $data['actv_letter'] = $actvLetterPath;
            }

            $submission->update($data);

            return redirect()->route('event.index')->with('success', 'Pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pengajuan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $submission = Submission::findOrFail($id);

            $files = ['file', 'ktp', 'appl_letter', 'actv_letter'];
            foreach ($files as $fileField) {
                if ($submission->$fileField && \Storage::disk('public_html_storage')->exists($submission->$fileField)) {
                    \Storage::disk('public_html_storage')->delete($submission->$fileField);
                }
            }

            $submission->delete();

            return redirect()->route('user.history')
                ->with('success', 'Pengajuan beserta semua file berhasil dihapus.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat menghapus pengajuan: ' . $e->getMessage());
        }
    }



    public function download($id, $type)
    {
        $submission = Submission::findOrFail($id);

        $map = [
            'proposal' => [
                'field' => 'file',
                'name' => 'proposal-' . $submission->id . '.pdf',
            ],
            'ktp' => [
                'field' => 'ktp',
                'name' => 'ktp-' . $submission->id . '.pdf',
            ],
            'appl_letter' => [
                'field' => 'appl_letter',
                'name' => 'surat-pengajuan-' . $submission->id . '.pdf',
            ],
            'actv_letter' => [
                'field' => 'actv_letter',
                'name' => 'surat-kegiatan-' . $submission->id . '.pdf',
            ],
        ];

        abort_if(!array_key_exists($type, $map), 404);

        $field = $map[$type]['field'];
        $downloadName = $map[$type]['name'];
        $path = $submission->{$field};

        if (!$path) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        // Normalisasi path jika ada data lama yang menyimpan "storage/..." atau full URL
        $path = str_replace(url('/storage') . '/', '', $path);
        $path = str_replace('/storage/', '', $path);
        $path = str_replace('storage/', '', $path);

        if (!Storage::disk('public_html_storage')->exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk('public_html_storage')->download($path, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
        ]);
    }
}
