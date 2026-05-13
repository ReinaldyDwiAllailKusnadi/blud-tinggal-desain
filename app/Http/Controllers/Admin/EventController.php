<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Submission;
use App\Models\Activity;
use App\Models\Content;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data event
        $eventQuery = Event::query();
        if ($request->filled('search')) {
            $eventQuery->where('vendor', 'like', '%' . $request->search . '%');
        }
        $events = $eventQuery->get()->map(function ($event) {
            $event->type = 'event'; // tambahkan tipe untuk identifikasi
            return $event;
        });
        // Ambil data submission
        $submissionQuery = Submission::query();
        $submissionQuery->where('status', 'approved'); // hanya ambil yang pending
        if ($request->filled('search')) {
            $submissionQuery->where('vendor', 'like', '%' . $request->search . '%');
        }
        $submissions = $submissionQuery->get()->map(function ($sub) {
            $sub->type = 'submission'; // tambahkan tipe untuk identifikasi
            return $sub;
        });

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['id', 'name_event', 'vendor', 'start_date'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $combined = $events->concat($submissions);
        $combined = $sortDir === 'asc' ? $combined->sortBy($sortBy)->values() : $combined->sortByDesc($sortBy)->values();

        // Manual paginate collection
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $pagedData = $combined->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $combined = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData, $combined->count(), $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.event.index', [
            'combined' => $combined,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);
    }

    public function create()
    {
        $contents = Content::all();
        return view('admin.event.create', compact('contents'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'content_id'  => 'required|exists:content,id',
                'vendor'     => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'name_event' => 'required|string|max:255',
                'file'       => 'nullable|file|mimes:pdf|max:2048',
            ]);

            $content = Content::findOrFail($data['content_id']);
            $data['location'] = $content->name;

            // Cek bentrok event
            $conflict = Event::where('content_id', $data['content_id'])
                ->where(function ($query) use ($data) {
                    $query->where('start_date', '<=', $data['end_date'])
                        ->where('end_date', '>=', $data['start_date']);
                })->first();

            if ($conflict) {
                return redirect()->back()
                    ->with('error', 'Tanggal yang dipilih bentrok dengan event lain: ' .
                        $conflict->name_event . ' oleh ' . $conflict->vendor .
                        ' (' . $conflict->start_date . ' s/d ' . $conflict->end_date . ')')
                    ->withInput($request->except(['start_date', 'end_date']));
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $rundownPath = $file->store('assets/rundowns', 'public_html_storage');
                $data['file'] = $rundownPath;
            }

            Event::create($data);

            Activity::create([
                'admin_id'    => auth('admin')->id(),
                'description' => 'menambahkan jadwal event baru.',
            ]);

            return redirect()->route('event.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan event: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $contents = Content::all();
        return view('admin.event.edit', compact('event', 'contents'));
    }

    public function update(Request $request, Event $event)
    {
        try {
            $data = $request->validate([
                'content_id' => 'required|exists:content,id',
                'vendor'     => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'name_event' => 'required|string|max:255',
                'file'       => 'nullable|file|mimes:pdf|max:2048',
            ]);

            $content = Content::findOrFail($data['content_id']);
            $data['location'] = $content->name;

            // Cek bentrok event (abaikan event saat ini)
            $conflict = Event::where('id', '!=', $event->id)
                ->where('content_id', $data['content_id'])
                ->where(function ($query) use ($data) {
                    $query->where('start_date', '<=', $data['end_date'])
                        ->where('end_date', '>=', $data['start_date']);
                })->first();

            if ($conflict) {
                return redirect()->back()
                    ->with('error', 'Tanggal yang dipilih bentrok dengan event lain: ' .
                        $conflict->name_event . ' oleh ' . $conflict->vendor .
                        ' (' . $conflict->start_date . ' s/d ' . $conflict->end_date . ')')
                    ->withInput($request->except(['start_date', 'end_date']));
            }

            // Jika ada file baru diupload (gunakan field 'file' agar konsisten dengan store)
            if ($request->hasFile('file')) {
                // Hapus file lama jika ada
                if ($event->file && Storage::disk('public_html_storage')->exists($event->file)) {
                    Storage::disk('public_html_storage')->delete($event->file);
                }

                // Simpan file baru
                $file = $request->file('file');
                $rundownPath = $file->store('assets/rundowns', 'public_html_storage');
                $data['file'] = $rundownPath;
            } else {
                // Tidak ada upload baru -> pertahankan file lama
                $data['file'] = $event->file;
            }

            // Update data
            $event->update($data);

            Activity::create([
                'admin_id'    => auth('admin')->id(),
                'description' => 'mengedit jadwal event.',
            ]);

            return redirect()->route('event.index')->with('success', 'Data berhasil diperbarui.');
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan saat memperbarui event: ' . $e->getMessage());
            }
        }
    
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('event.index')->with('success', 'Data berhasil dihapus.');
    }
}
