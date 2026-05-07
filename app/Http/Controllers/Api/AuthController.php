<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\User;
use Google\Client as GoogleClient;

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
                'username' => 'nullable|string|min:3|max:255|unique:users',
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
                'username.unique' => 'Username sudah digunakan.',
                'email.unique' => 'Email sudah digunakan.',
            ]);

            // Gunakan username dari request jika ada, jika tidak generate otomatis
            if ($request->has('username') && !empty($request->username)) {
                $username = $request->username;
            } else {
                // Auto-generate unique username
                $username = strtolower(str_replace(' ', '', $request->name));
                $originalUsername = $username;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }
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
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $clientId = config('services.google.client_id');

        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi Google Client ID belum tersedia.',
            ], 500);
        }

        $client = new GoogleClient([
            'client_id' => $clientId,
        ]);

        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Token Google tidak valid.',
            ], 401);
        }

        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? null;
        $picture = $payload['picture'] ?? null;

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Email Google tidak ditemukan.',
            ], 422);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $baseUsername = strtolower(strtok($email, '@'));
            $baseUsername = preg_replace('/[^a-z0-9._]/', '', $baseUsername);

            if (!$baseUsername) {
                $baseUsername = 'user';
            }

            $username = $baseUsername;
            $counter = 1;

            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $user = User::create([
                'name' => $name ?: $email,
                'username' => $username,
                'email' => $email,
                'phone' => null,
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            // Jangan overwrite data user secara agresif.
            // Cukup isi name jika masih kosong.
            if (!$user->name && $name) {
                $user->update([
                    'name' => $name,
                ]);
            }
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo' => $picture,
                ],
            ],
        ]);
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
