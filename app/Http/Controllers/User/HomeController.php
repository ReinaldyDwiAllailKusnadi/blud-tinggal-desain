<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Content;
use App\Models\News;
use App\Models\Event;
use App\Models\ContentFeature;
use App\Models\Submission;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Storage;


class HomeController extends Controller
{
    public function index()
    {
        $news = News::all();
        $contents = Content::all();
        return view('welcome', compact('contents','news'));
    }

    public function event()
    {
        $contents = Content::all();
        return view('user.event', compact('contents'));
    }

    public function booking($slug)
    {
        // Ambil lokasi berdasarkan slug
        $content = Content::where('slug', $slug)->first();

        if (!$content) {
            abort(404, 'Lokasi tidak ditemukan.');
        }

        // Ambil semua event untuk lokasi tersebut
        $events = Event::select('name_event', 'start_date', 'end_date', 'location')
            ->where('location', $content->name)
            ->get()
            ->map(function ($item) {
                $item->type = 'event';
                return $item;
            });

        // Ambil submission yang disetujui dan lokasi cocok
        $submissions = Submission::select('name_event', 'start_date', 'end_date', 'location')
            ->where('status', 'approved')
            ->where('location', $content->name)
            ->get()
            ->map(function ($item) {
                $item->type = 'submission';
                return $item;
            });

        // Gabungkan dan urutkan berdasarkan tanggal mulai
        $combined = $events->concat($submissions)->sortBy('start_date');

        // Group berdasarkan bulan
        $grouped = $combined->groupBy(function ($item) {
            return Carbon::parse($item->start_date)->translatedFormat('F');
        });

        return view('user.booking', [
            'events' => $grouped,
            'slug' => $slug,
        ]);
    }

    public function bookingDetail($slug, $bulan)
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

        // ubah bulan indo ke english kalau ada
        $bulanEn = $bulanMap[strtolower($bulan)] ?? $bulan;

        $monthNumber = \Carbon\Carbon::parse("1 $bulanEn")->month;

        $content = Content::where('slug', $slug)->firstOrFail();

        $events = Event::with('content')
            ->where('location', $content->name)
            ->whereMonth('start_date', $monthNumber)
            ->orderBy('start_date')
            ->get()
            ->map(function($event) {
                $event->pdf_file = $event->file;
                $event->type = 'event';
                return $event;
            });

        $submissions = Submission::where('status', 'approved')
            ->where('location', $content->name)
            ->whereMonth('start_date', $monthNumber)
            ->orderBy('start_date')
            ->get()
            ->map(function($submission) {
                $submission->pdf_file = $submission->actv_letter;
                $submission->type = 'submission';
                return $submission;
            });

        $merged = $events->merge($submissions)->sortBy('start_date')->values();

        return view('user.booking_detail', [
            'events' => $merged,
            'slug' => $slug,
            'bulan' => $bulan,
        ]);
    }


    public function content(){

        $contents = Content::all();
        return view('user.wisata', compact('contents'));
    }

    public function contentDetail($slug){

        $contents = Content::where('slug', $slug)->firstOrFail();
        return view('user.wisata_detail', compact('contents','slug'));
    }

    public function history(){

        $submissions = Submission::where('user_id', auth()->id())->orderBy('apply_date', 'desc')->orderBy('id', 'desc')->get();
        return view('user.history', compact('submissions'));
    }

    public function profile(){
        $user = Auth::user();

        return view('user.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|min:8',
        ]);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            $data['password'] = $user->password;
        }

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => $data['password'] ?? $user->password,
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function facility($slug)
    {
        // Cari content berdasarkan slug, bukan location
        $content = Content::where('slug', $slug)->firstOrFail();
    
        // Ambil fasilitas berdasarkan ID content
        $facilities = ContentFeature::where('location', $content->id)->get();
    
        return view('user.facility', compact('facilities', 'content'));
    }


    public function createSubmission()
    {
        $contents = Content::all();
        return view('user.form', compact('contents'));
    }
    
    public function storeSubmission(Request $request)
    {
        try {
            $rules = [
                'namePIC' => 'required|string|max:100',
                'no_hp' => 'required|string|max:12',
                'address' => 'required|string|max:255',
                'vendor' => 'required|string|max:100',
                'location' => 'required|exists:content,name',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'name_event' => 'required|string|max:255',
                'file' => 'nullable|file|mimes:pdf|max:5120',
                'ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'appl_letter' => 'nullable|file|mimes:pdf|max:5120',
                'actv_letter' => 'nullable|file|mimes:pdf|max:5120',
            ];

            $messages = [
                'file.mimes' => 'File proposal harus berformat PDF.',
                'file.max' => 'File proposal maksimal 5MB.',
                'ktp.required' => 'File KTP wajib diunggah.',
                'ktp.mimes' => 'File KTP harus berformat PDF, JPG, JPEG, atau PNG.',
                'ktp.max' => 'File KTP maksimal 5MB.',
                'appl_letter.mimes' => 'Surat permohonan harus berformat PDF.',
                'appl_letter.max' => 'Surat permohonan maksimal 5MB.',
                'actv_letter.mimes' => 'Surat keterangan kegiatan harus berformat PDF.',
                'actv_letter.max' => 'Surat keterangan kegiatan maksimal 5MB.',
                'location.exists' => 'Lokasi yang dipilih tidak valid.',
            ];

            $data = $request->validate($rules, $messages);

            $data['user_id'] = auth()->id();
            $content = Content::where('name', $data['location'])->firstOrFail();

            $data['status'] = 'pending';
            $data['apply_date'] = Carbon::now()->format('Y-m-d H:i');

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

            Submission::create($data);

            return redirect()->route('user.history')->with('success', 'Pengajuan berhasil dikirim. Harap tunggu informasi lebih lanjut.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $e->validator->errors()->first());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function download($id, $type)
    {
        $submission = Submission::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

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

        // Normalisasi path jika data lama menyimpan URL penuh atau prefix storage
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
