<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Exports\StudentAttendanceReportExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

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

            $attendances = StudentAttendance::with(['student', 'journal'])
                ->whereHas('journal', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                })
                ->whereHas('student', function ($query) use ($classroomId) {
                    $query->where('classroom_id', $classroomId);
                })
                ->get();

            $allStudentAttendances = $attendances;
            $grouped = $attendances->groupBy('student_id');

            foreach ($students as $student) {
                $studentAttendances = $grouped->get($student->id, collect());
                $absentRecords = $studentAttendances->whereIn('status', ['izin', 'sakit', 'alpa']);

                $groupedByDate = $absentRecords->groupBy(function ($att) {
                    return $att->journal->date->toDateString();
                });

                $izinCount = 0;
                $sakitCount = 0;
                $alpaCount = 0;

                foreach ($groupedByDate as $date => $items) {
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

        // 🔥 PERBAIKAN: Load relasi teachingSchedule dan schedules
        $attendances = StudentAttendance::with([
            'student',
            'journal.teachingSchedule.subject',
            'journal.schedules.subject', // many-to-many
            'journal.schedules.lessonHour'
        ])
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
            $journal = $att->journal;

            // 🔥 Ambil subject dari teachingSchedule (single) atau schedules (many-to-many)
            $subjectName = '-';
            if ($journal->teachingSchedule && $journal->teachingSchedule->subject) {
                $subjectName = $journal->teachingSchedule->subject->name;
            } elseif ($journal->schedules && $journal->schedules->isNotEmpty()) {
                // Ambil subject dari schedule pertama (semua schedule dalam blok punya subject yang sama)
                $firstSchedule = $journal->schedules->first();
                if ($firstSchedule && $firstSchedule->subject) {
                    $subjectName = $firstSchedule->subject->name;
                }
            }

            // 🔥 Ambil jam pelajaran dari schedules
            $sessionInfo = '';
            if ($journal->schedules && $journal->schedules->isNotEmpty()) {
                $sessions = $journal->schedules->pluck('lessonHour.session')->unique()->implode(', ');
                if ($sessions) {
                    $sessionInfo = ' (Jam ' . $sessions . ')';
                }
            }

            return [
                'date' => $journal->date->format('d/m/Y'),
                'day' => $journal->day_name,
                'status' => $att->status,
                'status_label' => $att->status_label,
                'status_badge' => $att->status_badge,
                'subject' => $subjectName . $sessionInfo,
                'material' => $journal->material ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'student_name' => $studentName,
            'data' => $data,
            'total' => $data->count()
        ]);
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $classroomId = $request->input('classroom_id');

        if (!$classroomId) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih kelas terlebih dahulu.'
            ], 422);
        }

        $classroom = Classroom::find($classroomId);
        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.'
            ], 404);
        }

        $students = Student::where('classroom_id', $classroomId)
            ->orderBy('name')
            ->get();

        $attendances = StudentAttendance::with(['student', 'journal'])
            ->whereHas('journal', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->whereHas('student', function ($query) use ($classroomId) {
                $query->where('classroom_id', $classroomId);
            })
            ->get();

        $grouped = $attendances->groupBy('student_id');
        $reportData = [];

        foreach ($students as $student) {
            $studentAttendances = $grouped->get($student->id, collect());
            $absentRecords = $studentAttendances->whereIn('status', ['izin', 'sakit', 'alpa']);

            $groupedByDate = $absentRecords->groupBy(function ($att) {
                return $att->journal->date->toDateString();
            });

            $izinCount = 0;
            $sakitCount = 0;
            $alpaCount = 0;

            foreach ($groupedByDate as $date => $items) {
                if ($items->contains('status', 'alpa')) {
                    $alpaCount++;
                } elseif ($items->contains('status', 'sakit')) {
                    $sakitCount++;
                } else {
                    $izinCount++;
                }
            }

            $reportData[] = [
                'nis' => $student->nis,
                'name' => $student->name,
                'izin' => $izinCount,
                'sakit' => $sakitCount,
                'alpa' => $alpaCount,
                'total_tidak_hadir' => $izinCount + $sakitCount + $alpaCount,
            ];
        }

        usort($reportData, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        if (empty($reportData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data presensi untuk periode ini.'
            ], 404);
        }

        $startFormatted = Carbon::parse($startDate)->format('d-m-Y');
        $endFormatted = Carbon::parse($endDate)->format('d-m-Y');
        $fileName = 'Rekap_Presensi_' . $classroom->name . '_' . $startFormatted . '_sampai_' . $endFormatted . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new StudentAttendanceReportExport($reportData, $classroom->name, $startFormatted, $endFormatted),
            $fileName
        );
    }
}
