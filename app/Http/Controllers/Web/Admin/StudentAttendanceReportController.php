<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('name')->get();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $classroomId = $request->input('classroom_id');

        $reportData = [];
        $selectedClassroom = null;
        $allStudentAttendances = [];

        if ($classroomId && $startDate && $endDate) {
            $selectedClassroom = Classroom::find($classroomId);

            $students = Student::where('classroom_id', $classroomId)
                ->orderBy('name')
                ->get();

            // Ambil semua data presensi siswa dalam rentang tanggal
            $attendances = StudentAttendance::with(['student', 'journal'])
                ->whereHas('journal', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                })
                ->whereHas('student', function ($query) use ($classroomId) {
                    $query->where('classroom_id', $classroomId);
                })
                ->get();

            // Simpan semua data presensi untuk modal detail
            $allStudentAttendances = $attendances;

            // Kelompokkan per siswa
            $grouped = $attendances->groupBy('student_id');

            foreach ($students as $student) {
                $studentAttendances = $grouped->get($student->id, collect());

                // 🔥 PERBAIKAN: Hitung per hari unik untuk status tidak hadir
                $absentRecords = $studentAttendances->whereIn('status', ['izin', 'sakit', 'alpa']);

                // Kelompokkan berdasarkan tanggal (unik per hari)
                $groupedByDate = $absentRecords->groupBy(function ($att) {
                    return $att->journal->date->toDateString();
                });

                // Inisialisasi counter
                $izinCount = 0;
                $sakitCount = 0;
                $alpaCount = 0;

                foreach ($groupedByDate as $date => $items) {
                    // Prioritas status: alpa > sakit > izin
                    if ($items->contains('status', 'alpa')) {
                        $alpaCount++;
                    } elseif ($items->contains('status', 'sakit')) {
                        $sakitCount++;
                    } else {
                        $izinCount++;
                    }
                }

                $reportData[] = [
                    'student_id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'izin' => $izinCount,
                    'sakit' => $sakitCount,
                    'alpa' => $alpaCount,
                    'total_tidak_hadir' => $izinCount + $sakitCount + $alpaCount,
                ];
            }

            // Urutkan berdasarkan nama
            usort($reportData, function ($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });
        }

        return view('admin.student-attendance-report.index', compact(
            'classrooms',
            'reportData',
            'selectedClassroom',
            'startDate',
            'endDate',
            'classroomId',
            'allStudentAttendances'
        ));
    }

    public function getStudentDetail(Request $request)
    {
        $studentId = $request->input('student_id');
        $classroomId = $request->input('classroom_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$studentId || !$classroomId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ], 422);
        }

        $attendances = StudentAttendance::with(['student', 'journal.teachingSchedule.subject'])
            ->where('student_id', $studentId)
            ->whereHas('student', function ($query) use ($classroomId) {
                $query->where('classroom_id', $classroomId);
            })
            ->whereHas('journal', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->whereIn('status', ['izin', 'sakit', 'alpa'])
            ->orderBy('created_at', 'desc')
            ->get();

        $studentName = $attendances->first()?->student->name ?? '';

        $data = $attendances->map(function ($att) {
            return [
                'date' => $att->journal->date->format('d/m/Y'),
                'day' => $att->journal->day_name,
                'status' => $att->status,
                'status_label' => $att->status_label,
                'status_badge' => $att->status_badge,
                'subject' => $att->journal->teachingSchedule->subject->name ?? '-',
                'material' => $att->journal->material ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'student_name' => $studentName,
            'data' => $data,
            'total' => $data->count()
        ]);
    }
}
