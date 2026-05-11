<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\UserResetPasswordCodeMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Mengirim kode reset 6 digit ke email user
     */
    public function sendResetCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            // Tetap return sukses generik jika user tidak ditemukan agar tidak membocorkan data
            if (!$user) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jika email terdaftar, kode reset password akan dikirim.',
                ]);
            }

            // Generate kode 6 digit
            $code = random_int(100000, 999999);

            // Hapus token lama untuk email ini
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Simpan token baru (dihash)
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make((string)$code),
                'created_at' => now(),
            ]);

            // Kirim email
            try {
                Mail::to($request->email)->send(new UserResetPasswordCodeMail($code));
            } catch (\Exception $e) {
                Log::error('Mail sending error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim kode reset password. Silakan coba lagi.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Jika email terdaftar, kode reset password akan dikirim.',
            ]);

        } catch (\Exception $e) {
            Log::error('ForgotPassword API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
            ], 500);
        }
    }

    /**
     * Verifikasi kode reset tanpa mengubah password
     */
    public function verifyResetCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|string|min:6|max:6',
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.exists' => 'Email tidak ditemukan.',
                'code.required' => 'Kode reset wajib diisi.',
                'code.min' => 'Kode reset harus 6 digit.',
                'code.max' => 'Kode reset harus 6 digit.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$reset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode reset tidak valid atau sudah kedaluwarsa.',
                ], 422);
            }

            // Cek kedaluwarsa (15 menit)
            if (Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode reset sudah kedaluwarsa.',
                ], 422);
            }

            // Verifikasi kode
            if (!Hash::check($request->code, $reset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode reset tidak valid.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode reset valid.',
            ]);

        } catch (\Exception $e) {
            Log::error('VerifyResetCode API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
            ], 500);
        }
    }

    /**
     * Reset password menggunakan kode
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|string|min:6|max:6',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.exists' => 'Email tidak ditemukan.',
                'code.required' => 'Kode reset wajib diisi.',
                'code.min' => 'Kode reset harus 6 digit.',
                'code.max' => 'Kode reset harus 6 digit.',
                'password.required' => 'Password baru wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $reset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$reset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode reset tidak valid atau sudah kedaluwarsa.',
                ], 422);
            }

            // Cek kedaluwarsa (15 menit)
            if (Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode reset sudah kedaluwarsa.',
                ], 422);
            }

            // Verifikasi kode
            if (!Hash::check($request->code, $reset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode reset tidak valid.',
                ], 422);
            }

            // Update Password
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Bersihkan token reset
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Revoke semua token Sanctum (paksa logout semua device)
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah. Silakan login kembali.',
            ]);

        } catch (\Exception $e) {
            Log::error('ResetPassword API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
            ], 500);
        }
    }
}
