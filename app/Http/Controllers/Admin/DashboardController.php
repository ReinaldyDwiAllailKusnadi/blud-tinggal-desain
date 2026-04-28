<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Submission;
use App\Models\User;
use App\Models\Activity;
use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $activities = Activity::with('admin')->latest()->take(5)->get();

        // Ambil 6 bulan terakhir
        $dates = collect(range(0, 5))->map(function ($i) {
            return \Carbon\Carbon::now()->subMonths($i)->format('F Y');
        })->reverse()->values();

        // Hitung jumlah submission untuk setiap bulan
        $counts = collect(range(0, 5))->map(function ($i) {
            $month = \Carbon\Carbon::now()->subMonths($i);
            return Submission::whereYear('created_at', $month->year)
                             ->whereMonth('created_at', $month->month)
                             ->count();
        })->reverse()->values();

        return view('admin.dashboard', compact('userCount', 'activities', 'dates', 'counts'));
    }


}
