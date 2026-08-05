<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FcmTokenController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_name' => 'nullable|string',
            'device_platform' => 'nullable|string|in:android,ios',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        UserFcmToken::updateOrCreate(
            ['user_id' => $user->id, 'fcm_token' => $request->fcm_token],
            [
                'device_name' => $request->device_name,
                'device_platform' => $request->device_platform,
                'last_used_at' => now(),
            ]
        );

        // Hapus token lama dengan device_name yang sama (jika berganti token)
        UserFcmToken::where('user_id', $user->id)
            ->where('fcm_token', '!=', $request->fcm_token)
            ->where('device_name', $request->device_name)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Token berhasil didaftarkan']);
    }

    public function unregister(Request $request)
    {
        $validator = Validator::make($request->all(), ['fcm_token' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        UserFcmToken::where('user_id', $request->user()->id)
            ->where('fcm_token', $request->fcm_token)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Token berhasil dihapus']);
    }
}
