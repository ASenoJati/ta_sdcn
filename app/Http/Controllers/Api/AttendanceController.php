<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\RoleAttendanceTime;
use App\Models\UserAttendance;
use App\Traits\GeoLocationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use GeoLocationTrait;

    /**
     * Menampilkan History Absensi User
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Ambil bulan dan tahun dari request (default ke bulan/tahun sekarang)
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $history = UserAttendance::where('user_id', $user->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->orderBy('attendance_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => "History absensi bulan $month tahun $year",
            'data' => $history
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'image'     => 'required|image|max:2048',
        ]);

        $user = $request->user();
        $today = now()->startOfDay();

        // 1. Cek Double Check-in
        if (UserAttendance::where('user_id', $user->id)->whereDate('attendance_date', $today)->exists()) {
            return response()->json(['message' => 'Anda sudah melakukan check-in hari ini'], 422);
        }

        // 2. Ambil Lokasi Default (Cache atau Singleton lebih baik)
        $location = Location::where('default', true)->first();
        if (!$location) return response()->json(['message' => 'Titik lokasi kantor belum diatur'], 500);

        // 3. Hitung Jarak
        $distance = $this->calculateDistance($location->latitude, $location->longitude, $request->latitude, $request->longitude);

        // 4. Hitung Status Berdasarkan Waktu & Jarak
        $attendanceTime = RoleAttendanceTime::where('role_id', $user->role_id)->with('setting')->first();
        if (!$attendanceTime) return response()->json(['message' => 'Jadwal absen role Anda belum diatur'], 422);

        $status = $this->determineCheckInStatus($distance, $location->radius_km * 1000, $attendanceTime->setting);

        // 5. Simpan Image
        $path = $request->file('image')->store('attendance/check_in', 'public');

        // 6. Simpan Absensi
        $attendance = UserAttendance::create([
            'user_id' => $user->id,
            'instance_location_id' => $location->id,
            'attendance_date' => $today,
            'check_in_time' => now(),
            'check_in_status' => $status->value,
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'image_in' => $path,
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Check-in berhasil',
            'data' => $attendance
        ]);
    }

    private function determineCheckInStatus($distance, $maxDistance, $setting)
    {
        // Cek Jarak Terlebih Dahulu
        if ($distance > $maxDistance) {
            return AttendanceStatus::OUT_OF_RANGE;
        }

        $now = now();
        $startTime = Carbon::parse($setting->check_in_start);
        $endTime = Carbon::parse($setting->check_in_end)->addMinutes($setting->grace_period_minutes);

        if ($now->lt($startTime)) return AttendanceStatus::TO_EARLY;
        if ($now->gt($endTime)) return AttendanceStatus::LATE;

        return AttendanceStatus::PRESENT;
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        $attendance = UserAttendance::where('user_id', $user->id)
            ->whereDate('attendance_date', now()->startOfDay())
            ->first();

        if (!$attendance) return response()->json(['message' => 'Anda belum check-in hari ini'], 404);
        if ($attendance->check_out_time) return response()->json(['message' => 'Anda sudah check-out'], 422);

        // ... Logika Checkout mirip dengan Checkin (Cek Jarak & Waktu) ...
        // Gunakan determineCheckOutStatus()

        $path = $request->file('image')->store('attendance/check_out', 'public');

        $attendance->update([
            'check_out_time' => now(),
            'check_out_status' => AttendanceStatus::PRESENT, // Contoh simple
            'image_out' => $path,
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
        ]);

        return response()->json(['message' => 'Check-out berhasil']);
    }
}
