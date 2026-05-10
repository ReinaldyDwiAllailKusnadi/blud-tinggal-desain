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

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::query()->where('status', 'pending');

        if ($request->filled('search')) {
            $query->where('vendor', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('location')) {
            $query->where('location', $request->location);
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
            $query->where('location', $request->location);
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
            $query->where('location', $request->location);
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

        try {
            // kirim email ke user
            if ($submission->user && $submission->user->email) {
                Mail::to($submission->user->email)
                    ->send(new BookingStatusMail($submission, 'approved'));
            }

            return redirect()->back()->with('success', 'Pengajuan disetujui dan email notifikasi berhasil dikirim.');
        } catch (\Throwable $e) {
            Log::warning('Email notifikasi approval gagal dikirim', [
                'submission_id' => $submission->id,
                'user_id' => $submission->user_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('success', 'Pengajuan berhasil disetujui.')
                ->with('warning', 'Namun email notifikasi gagal dikirim. Silakan cek konfigurasi email.');
        }
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

        try {
            // kirim email ke user
            if ($submission->user && $submission->user->email) {
                Mail::to($submission->user->email)
                    ->send(new BookingStatusMail($submission, 'rejected'));
            }

            return redirect()->back()->with('success', 'Pengajuan ditolak dan email notifikasi berhasil dikirim.');
        } catch (\Throwable $e) {
            Log::warning('Email notifikasi rejection gagal dikirim', [
                'submission_id' => $submission->id,
                'user_id' => $submission->user_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('success', 'Pengajuan berhasil ditolak.')
                ->with('warning', 'Namun email notifikasi gagal dikirim. Silakan cek konfigurasi email.');
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
                'location'   => 'required|exists:content,name',
                'start_date' => 'required|date',
                'end_date'   => 'required|date',
                'name_event' => 'required|string|max:255',
                'file'       => 'nullable|file|mimes:pdf|max:5048',
                'ktp'        => 'nullable|file|mimes:pdf|max:5048',
                'appl_letter'=> 'nullable|file|mimes:pdf|max:5048',
                'actv_letter'=> 'nullable|file|mimes:pdf|max:5048',
            ]);

            $content = Content::where('name', $data['location'])->firstOrFail();

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
        
        $mapping = [
            'proposal'    => 'file',
            'ktp'         => 'ktp',
            'appl_letter' => 'appl_letter',
            'actv_letter' => 'actv_letter',
        ];

        if (!isset($mapping[$type])) {
            abort(404, 'Tipe file tidak valid.');
        }

        $field = $mapping[$type];
        $path = $submission->$field;

        if (!$path || !\Storage::disk('public_html_storage')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Penamaan file yang lebih rapi
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = "{$type}-{$submission->id}.{$extension}";

        if ($type === 'appl_letter') $filename = "surat-pengajuan-{$submission->id}.{$extension}";
        if ($type === 'actv_letter') $filename = "surat-kegiatan-{$submission->id}.{$extension}";

        return \Storage::disk('public_html_storage')->download($path, $filename);
    }
}
