<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Location;
use App\Models\TeachingSchedule;
use App\Models\TeachingJournal;
use App\Models\UserAttendance;
use App\Models\LessonHour;
use App\Models\AttendanceTimeSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Statistik Utama
        $stats = [
            'total_teachers' => User::where('role_id', 2)->count(),
            'total_students' => Student::count(),
            'total_classrooms' => Classroom::count(),
            'total_subjects' => Subject::count(),
            'total_locations' => Location::count(),
            'total_lesson_hours' => LessonHour::count(),
            'total_attendance_settings' => AttendanceTimeSetting::count(),
            'total_teaching_schedules' => TeachingSchedule::count(),
        ];

        // Statistik Presensi Hari Ini
        $today = Carbon::today();
        $todayAttendance = UserAttendance::whereDate('attendance_date', $today)->get();

        $attendanceStats = [
            'checked_in' => $todayAttendance->whereNotNull('check_in_time')->count(),
            'checked_out' => $todayAttendance->whereNotNull('check_out_time')->count(),
            'on_time' => $todayAttendance->where('check_in_status', 'present')->count(),
            'late' => $todayAttendance->where('check_in_status', 'late')->count(),
        ];

        // Statistik Jurnal Pembelajaran
        $journalStats = [
            'total_journals' => TeachingJournal::count(),
            'this_month_journals' => TeachingJournal::whereMonth('created_at', Carbon::now()->month)->count(),
            'this_week_journals' => TeachingJournal::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];

        // Data untuk Chart - Presensi 7 Hari Terakhir
        $attendanceChart = $this->getAttendanceChartData();

        // Data untuk Chart - Jurnal per Bulan
        $journalChart = $this->getJournalChartData();

        // Data untuk Chart - Top Mata Pelajaran
        $topSubjects = $this->getTopSubjects();

        // Data untuk Chart - Status Presensi
        $attendanceStatusChart = $this->getAttendanceStatusChart();

        // Jadwal Hari Ini
        $todaySchedules = $this->getTodaySchedules();

        // Aktivitas Terbaru
        $recentActivities = $this->getRecentActivities();

        // Data untuk Map (Lokasi Presensi Hari Ini)
        $attendanceLocations = $this->getAttendanceLocations();

        return view('admin.dashboard.index', compact(
            'stats',
            'attendanceStats',
            'journalStats',
            'attendanceChart',
            'journalChart',
            'topSubjects',
            'attendanceStatusChart',
            'todaySchedules',
            'recentActivities',
            'attendanceLocations'
        ));
    }

    /**
     * Get attendance chart data for last 7 days.
     */
    private function getAttendanceChartData()
    {
        $labels = [];
        $checkInData = [];
        $checkOutData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            $attendance = UserAttendance::whereDate('attendance_date', $date);
            $checkInData[] = (clone $attendance)->whereNotNull('check_in_time')->count();
            $checkOutData[] = (clone $attendance)->whereNotNull('check_out_time')->count();
        }

        return [
            'labels' => $labels,
            'check_in' => $checkInData,
            'check_out' => $checkOutData,
        ];
    }

    /**
     * Get journal chart data per month.
     */
    private function getJournalChartData()
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('F Y');

            $data[] = TeachingJournal::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get top subjects by schedule count.
     */
    private function getTopSubjects()
    {
        return TeachingSchedule::select('subjects.name', DB::raw('COUNT(*) as total'))
            ->join('subjects', 'teaching_schedules.subject_id', '=', 'subjects.id')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get attendance status chart data.
     */
    private function getAttendanceStatusChart()
    {
        $today = Carbon::today();

        $present = UserAttendance::whereDate('attendance_date', $today)
            ->where('check_in_status', 'present')
            ->count();

        $late = UserAttendance::whereDate('attendance_date', $today)
            ->where('check_in_status', 'late')
            ->count();

        $absent = User::where('role_id', 2)
            ->whereDoesntHave('attendances', function ($query) use ($today) {
                $query->whereDate('attendance_date', $today);
            })
            ->count();

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
        ];
    }

    /**
     * Get today's schedules.
     */
    private function getTodaySchedules()
    {
        $todayName = Carbon::now()->format('l');

        return TeachingSchedule::with(['teacher', 'subject', 'classroom', 'lessonHour'])
            ->where('day', $todayName)
            ->orderBy('lesson_hour_id')
            ->limit(10)
            ->get();
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // Recent attendances
        $recentAttendances = UserAttendance::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'attendance',
                    'title' => 'Presensi Guru',
                    'description' => $item->user->name . ' melakukan presensi pada ' . $item->attendance_date->format('d/m/Y'),
                    'time' => $item->created_at->diffForHumans(),
                    'icon' => 'bi-calendar-check',
                    'color' => 'success'
                ];
            });

        // Recent journals
        $recentJournals = TeachingJournal::with('teachingSchedule.subject')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'journal',
                    'title' => 'Jurnal Pembelajaran',
                    'description' => 'Jurnal ' . $item->teachingSchedule->subject->name . ' ditambahkan',
                    'time' => $item->created_at->diffForHumans(),
                    'icon' => 'bi-journal-bookmark-fill',
                    'color' => 'primary'
                ];
            });

        // Recent students
        $recentStudents = Student::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'student',
                    'title' => 'Siswa Baru',
                    'description' => 'Siswa ' . $item->name . ' (NIS: ' . $item->nis . ') ditambahkan',
                    'time' => $item->created_at->diffForHumans(),
                    'icon' => 'bi-people-fill',
                    'color' => 'info'
                ];
            });

        // Merge and sort
        $activities = $recentAttendances->concat($recentJournals)->concat($recentStudents)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return $activities;
    }

    /**
     * Get attendance locations for map.
     */
    private function getAttendanceLocations()
    {
        $today = Carbon::today();

        return UserAttendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in_latitude')
            ->whereNotNull('check_in_longitude')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->user->name,
                    'latitude' => $item->check_in_latitude,
                    'longitude' => $item->check_in_longitude,
                    'time' => $item->check_in_time->format('H:i'),
                    'status' => $item->check_in_status,
                ];
            });
    }

    /**
     * Get chart data for API (AJAX refresh).
     */
    public function getChartData()
    {
        return response()->json([
            'attendanceChart' => $this->getAttendanceChartData(),
            'journalChart' => $this->getJournalChartData(),
            'attendanceStatusChart' => $this->getAttendanceStatusChart(),
        ]);
    }
}
