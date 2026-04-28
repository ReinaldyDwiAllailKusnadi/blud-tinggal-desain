<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|min:3|max:255',
                'phone' => [
                    'nullable',
                    'string',
                    'max:15',
                    'regex:/^(08|628)[0-9]{7,12}$/',
                ],
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:6',
            ], [
                'name.min' => 'Nama minimal harus 3 huruf.',
                'phone.regex' => 'Nomor HP harus diawali dengan 08 atau 628.',
            ]);

            // Auto-generate unique username
            $username = strtolower(str_replace(' ', '', $request->name));
            $originalUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }

            $user = User::create([
                'name' => $request->name,
                'username' => $username,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil.',
                'data' => [
                    'user' => $user->only(['id', 'username', 'name', 'email', 'phone']),
                    'token' => $token,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Register API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'user' => $user->only(['id', 'username', 'name', 'email', 'phone']),
                    'token' => $token,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
            ], 500);
        }
    }

    /**
     * Google Sign-In: Flutter mengirim ID token Google, Laravel memverifikasi dan login/register
     */
    public function googleLogin(Request $request)
    {
        try {
            $request->validate([
                'id_token' => 'required|string',
            ]);

            // Verify Google ID token using Socialite
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->id_token);

            if (!$googleUser || !$googleUser->getEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google tidak valid.',
                ], 401);
            }

            // Cari user atau buat baru
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $username = strtolower(str_replace(' ', '', $googleUser->getName()));
                $originalUsername = $username;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                ]);
            }

            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Google berhasil.',
                'data' => [
                    'user' => $user->only(['id', 'username', 'name', 'email', 'phone']),
                    'token' => $token,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Google Login API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal login dengan Google.',
            ], 500);
        }
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}
