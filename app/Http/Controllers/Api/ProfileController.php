<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        // Ambil user yang login beserta role dan jadwal waktunya
        $user = $request->user()->load([
            'role',
            'roleAttendance.setting'
        ]);

        // Transformasi data agar rapi saat diterima Flutter
        $schedule = $user->roleAttendance?->setting;

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->name,
                ],
                'schedule' => $schedule ? [
                    'name' => $schedule->name,
                    'check_in_start' => $schedule->check_in_start,
                    'check_in_end' => $schedule->check_in_end,
                    'check_out_start' => $schedule->check_out_start,
                    'check_out_end' => $schedule->check_out_end,
                    'grace_period' => $schedule->grace_period_minutes . ' menit',
                ] : null
            ]
        ]);
    }
}
