<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required', // Penting untuk tracking token di mobile
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credentials mismatch'], 401);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'role' => $user->getRoleNames()->first(),
            'user' => $user
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        // Di Flutter, biasanya Anda mengirim ID Token dari Google Sign-In plugin
        $googleUser = Socialite::driver('google')->userFromToken($request->token);

        // Filter: Hanya email yang sudah ada di database kita
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email anda tidak terdaftar di sistem. Silahkan hubungi admin.'
            ], 403);
        }

        // Update data google jika perlu
        $user->update(['google_id' => $googleUser->getId()]);

        return response()->json([
            'token' => $user->createToken('google-mobile-login')->plainTextToken,
            'user' => $user,
            'role' => $user->getRoleNames()->first()
        ]);
    }

    /**
     * Logout User (Revoke Token)
     */
    public function logout(Request $request)
    {
        // Menghapus token yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout, token telah dihapus.'
        ]);
    }
}
