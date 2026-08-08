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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
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
        $attendanceStats = $this->getAttendanceStats($today);

        // Statistik Jurnal Pembelajaran
        $journalStats = $this->getJournalStats();

        // Data untuk Chart
        $attendanceChart = $this->getAttendanceChartData();
        $journalChart = $this->getJournalChartData();
        $topSubjects = $this->getTopSubjects();
        $attendanceStatusChart = $this->getAttendanceStatusChart($today);

        // Jadwal Hari Ini
        $todaySchedules = $this->getTodaySchedules();

        // Aktivitas Terbaru
        $recentActivities = $this->getRecentActivities();

        // Data untuk Map
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

    private function getAttendanceStats($today)
    {
        $todayAttendance = UserAttendance::whereDate('attendance_date', $today)->get();

        return [
            'checked_in' => $todayAttendance->whereNotNull('check_in_time')->count(),
            'checked_out' => $todayAttendance->whereNotNull('check_out_time')->count(),
            'on_time' => $todayAttendance->where('check_in_status', 'present')->count(),
            'late' => $todayAttendance->where('check_in_status', 'late')->count(),
        ];
    }

    private function getJournalStats()
    {
        return [
            'total_journals' => TeachingJournal::count(),
            'this_month_journals' => TeachingJournal::whereMonth('created_at', Carbon::now()->month)->count(),
            'this_week_journals' => TeachingJournal::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];
    }

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

    private function getTopSubjects()
    {
        return TeachingSchedule::select('subjects.name', DB::raw('COUNT(*) as total'))
            ->join('subjects', 'teaching_schedules.subject_id', '=', 'subjects.id')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }

    private function getAttendanceStatusChart($today)
    {
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

    private function getTodaySchedules()
    {
        $todayName = Carbon::now()->format('l');

        return TeachingSchedule::with(['teacher', 'subject', 'classroom', 'lessonHour'])
            ->where('day', $todayName)
            ->orderBy('lesson_hour_id')
            ->limit(10)
            ->get()
            ->map(function ($schedule) {
                return (object) [
                    'id' => $schedule->id,
                    'teacher_name' => $schedule->teacher?->name ?? '-',
                    'subject_name' => $schedule->subject?->name ?? '-',
                    'classroom_name' => $schedule->classroom?->name ?? '-',
                    'lesson_hour' => $schedule->lessonHour ?
                        $schedule->lessonHour->start_time . ' - ' . $schedule->lessonHour->end_time :
                        '-',
                    'day' => $schedule->day,
                    'original' => $schedule
                ];
            });
    }

    private function getRecentActivities()
    {
        // Recent attendances
        $recentAttendances = UserAttendance::with('user')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'attendance',
                    'title' => 'Presensi Guru',
                    'description' => $item->user->name . ' melakukan presensi pada ' . $item->attendance_date->format('d/m/Y'),
                    'time' => $item->created_at->diffForHumans(),
                    'created_at' => $item->created_at,
                    'icon' => 'bi-calendar-check',
                    'color' => 'success'
                ];
            });

        // Recent journals
        $recentJournals = TeachingJournal::with(['teachingSchedule.subject', 'teachingSchedule.teacher'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $subjectName = $item->teachingSchedule?->subject?->name ?? 'Mata Pelajaran tidak tersedia';
                return [
                    'type' => 'journal',
                    'title' => 'Jurnal Pembelajaran',
                    'description' => 'Jurnal ' . $subjectName . ' ditambahkan',
                    'time' => $item->created_at->diffForHumans(),
                    'created_at' => $item->created_at,
                    'icon' => 'bi-journal-bookmark-fill',
                    'color' => 'primary'
                ];
            });

        // Recent students
        $recentStudents = Student::latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'student',
                    'title' => 'Siswa Baru',
                    'description' => 'Siswa ' . $item->name . ' (NIS: ' . $item->nis . ') ditambahkan',
                    'time' => $item->created_at->diffForHumans(),
                    'created_at' => $item->created_at,
                    'icon' => 'bi-people-fill',
                    'color' => 'info'
                ];
            });

        return $recentAttendances
            ->concat($recentJournals)
            ->concat($recentStudents)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
    }

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

    public function getChartData(Request $request)
    {
        $today = Carbon::today();

        return response()->json([
            'attendanceChart' => $this->getAttendanceChartData(),
            'journalChart' => $this->getJournalChartData(),
            'attendanceStatusChart' => $this->getAttendanceStatusChart($today),
        ]);
    }
}
