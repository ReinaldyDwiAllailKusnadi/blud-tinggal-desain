<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Mail\UserResetPasswordCodeMail;
use Carbon\Carbon;

class WebForgotPasswordController extends Controller
{
    /**
     * Tampilkan form lupa password
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim kode reset ke email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Tetap simpan email ke session agar flow berlanjut
        Session::put('reset_email', $request->email);

        if ($user) {
            // Generate kode 6 digit
            $code = random_int(100000, 999999);

            // Simpan token (hash)
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make((string)$code),
                    'created_at' => now(),
                ]
            );

            // Kirim email
            try {
                Mail::to($request->email)->send(new UserResetPasswordCodeMail($code));
            } catch (\Exception $e) {
                Log::error('Web Forgot Password Email Error: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
            }
        }

        return redirect()->route('forgot.password.verify.form')->with('success', 'Jika email terdaftar, kode reset password akan dikirim.');
    }

    /**
     * Tampilkan form verifikasi kode
     */
    public function showVerifyForm()
    {
        if (!Session::has('reset_email')) {
            return redirect()->route('forgot.password.form');
        }

        return view('auth.verify-reset-code');
    }

    /**
     * Verifikasi kode
     */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:6|max:6',
        ], [
            'code.required' => 'Kode reset wajib diisi.',
            'code.min' => 'Kode reset harus 6 digit.',
            'code.max' => 'Kode reset harus 6 digit.',
        ]);

        $email = Session::get('reset_email');
        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$reset) {
            return back()->with('error', 'Kode reset tidak valid atau sudah kedaluwarsa.');
        }

        // Cek kedaluwarsa (15 menit)
        if (Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            Session::forget('reset_email');
            return redirect()->route('forgot.password.form')->with('error', 'Kode reset sudah kedaluwarsa. Silakan kirim ulang kode.');
        }

        // Verifikasi
        if (!Hash::check($request->code, $reset->token)) {
            return back()->with('error', 'Kode reset tidak valid.');
        }

        // Simpan status verifikasi ke session
        Session::put('reset_code_verified', true);
        Session::put('reset_code', $request->code);

        return redirect()->route('forgot.password.new.form');
    }

    /**
     * Tampilkan form password baru
     */
    public function showNewPasswordForm()
    {
        if (!Session::has('reset_email') || !Session::get('reset_code_verified')) {
            return redirect()->route('forgot.password.form');
        }

        return view('auth.new-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $email = Session::get('reset_email');
        $code = Session::get('reset_code');

        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$reset || !Hash::check($code, $reset->token) || Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
            return redirect()->route('forgot.password.form')->with('error', 'Sesi reset password tidak valid atau kedaluwarsa.');
        }

        // Update password
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Revoke Sanctum tokens
            $user->tokens()->delete();

            // Bersihkan token & session
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            Session::forget(['reset_email', 'reset_code', 'reset_code_verified']);

            return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login kembali.');
        }

        return redirect()->route('login')->with('error', 'User tidak ditemukan.');
    }
}
