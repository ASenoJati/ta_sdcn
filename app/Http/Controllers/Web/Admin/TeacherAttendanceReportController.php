<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAttendance;
use App\Exports\TeacherAttendanceExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TeacherAttendanceReportController extends Controller
{
    public function index()
    {
        $teachers = User::where('role_id', 2)->orderBy('name')->get();
        return view('admin.teacher-attendance-report.index', compact('teachers'));
    }

    public function getData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $teacherId = $request->input('user_id');

        $query = UserAttendance::with('user')
            ->whereDate('attendance_date', '>=', $startDate)
            ->whereDate('attendance_date', '<=', $endDate);

        if ($teacherId) {
            $query->where('user_id', $teacherId);
        }

        $attendances = $query->get();

        $grouped = $attendances->groupBy('user_id');

        $data = [];
        foreach ($grouped as $userId => $items) {
            $user = $items->first()->user;
            $hadir = $items->where('check_in_status', 'present')->count();
            $late = $items->where('check_in_status', 'late')->count();
            $total = $items->count();

            $data[] = [
                'user_id' => $userId,
                'name' => $user->name,
                'hadir' => $hadir,
                'late' => $late,
                'total' => $total,
                'hadir_percentage' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
            ];
        }

        return response()->json($data);
    }

    public function getChartData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $teacherId = $request->input('user_id');

        $query = UserAttendance::with('user')
            ->whereDate('attendance_date', '>=', $startDate)
            ->whereDate('attendance_date', '<=', $endDate);

        if ($teacherId) {
            $query->where('user_id', $teacherId);
        }

        $attendances = $query->get();

        if ($attendances->isEmpty()) {
            return response()->json([
                'labels' => [],
                'present' => [],
                'late' => [],
            ]);
        }

        $grouped = $attendances->groupBy(function ($item) {
            return $item->attendance_date->format('Y-m-d');
        });

        $labels = [];
        $presentData = [];
        $lateData = [];

        $sortedDates = $grouped->keys()->sort();

        foreach ($sortedDates as $date) {
            $items = $grouped[$date];
            $labels[] = Carbon::parse($date)->translatedFormat('d M');
            $presentData[] = $items->where('check_in_status', 'present')->count();
            $lateData[] = $items->where('check_in_status', 'late')->count();
        }

        return response()->json([
            'labels' => $labels,
            'present' => $presentData,
            'late' => $lateData,
        ]);
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $teacherId = $request->input('user_id');

        $query = UserAttendance::with('user')
            ->whereDate('attendance_date', '>=', $startDate)
            ->whereDate('attendance_date', '<=', $endDate);

        if ($teacherId) {
            $query->where('user_id', $teacherId);
        }

        $attendances = $query->get()->groupBy('user_id');

        if ($attendances->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data presensi untuk rentang tanggal yang dipilih.'
            ], 404);
        }

        $data = [];
        foreach ($attendances as $userId => $items) {
            $user = $items->first()->user;
            $hadir = $items->where('check_in_status', 'present')->count();
            $late = $items->where('check_in_status', 'late')->count();
            $total = $items->count();

            $data[] = [
                'Nama Guru' => $user->name,
                'Hadir' => $hadir,
                'Terlambat' => $late,
                'Total Presensi' => $total,
                'Persentase Kehadiran' => $total > 0 ? round(($hadir / $total) * 100, 2) . '%' : '0%',
            ];
        }

        $startDateFormatted = Carbon::parse($startDate)->format('d-m-Y');
        $endDateFormatted = Carbon::parse($endDate)->format('d-m-Y');
        $fileName = 'Rekap_Presensi_Guru_' . $startDateFormatted . '_sampai_' . $endDateFormatted . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new TeacherAttendanceExport($data), $fileName);
    }
}
