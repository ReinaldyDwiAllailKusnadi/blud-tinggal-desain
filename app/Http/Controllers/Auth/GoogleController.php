<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Generate username dari email
                $baseUsername = strtolower(strtok($googleUser->getEmail(), '@'));
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
                    'name' => $googleUser->getName() ?: $googleUser->getEmail(),
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'phone' => null,
                    'password' => bcrypt(Str::random(32)),
                ]);
            } else {
                // Update nama jika masih kosong
                if (!$user->name && $googleUser->getName()) {
                    $user->update([
                        'name' => $googleUser->getName(),
                    ]);
                }
            }

            Auth::login($user);

            return redirect('/')->with('success', 'Login Google berhasil.');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }


}
