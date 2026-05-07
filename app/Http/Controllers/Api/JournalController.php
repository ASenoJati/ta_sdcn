<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    /**
     * 1. Jadwal Mengajar Hari Ini
     */
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $dayName = now()->format('l');

        $schedules = TeachingSchedule::with([
            'subject',
            'classroom',
            'lessonHour'
        ])
            ->where('user_id', $request->user()->id)
            ->where('day', $dayName)
            ->orderBy('lesson_hour_id')
            ->get()
            ->map(function ($schedule) use ($today) {

                $isDone = TeachingJournal::where(
                    'teaching_schedule_id',
                    $schedule->id
                )
                    ->whereDate('date', $today)
                    ->exists();

                return [
                    'id' => $schedule->id,

                    'subject' => [
                        'id' => $schedule->subject->id,
                        'name' => $schedule->subject->name,
                    ],

                    'classroom' => [
                        'id' => $schedule->classroom->id,
                        'name' => $schedule->classroom->name,
                    ],

                    'lesson_hour' => [
                        'id' => $schedule->lessonHour->id,
                        'session' => $schedule->lessonHour->session,
                        'start_time' => $schedule->lessonHour->start_time,
                        'end_time' => $schedule->lessonHour->end_time,
                    ],

                    'day' => $schedule->day,

                    'is_journal_filled' => $isDone,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    /**
     * 2. Detail Jurnal
     */
    public function detail($scheduleId)
    {
        $today = now()->toDateString();

        $journal = TeachingJournal::with([
            'attendances.student',
            'teachingSchedule.lessonHour',
            'teachingSchedule.subject',
            'teachingSchedule.classroom',
        ])
            ->where('teaching_schedule_id', $scheduleId)
            ->whereDate('date', $today)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $journal->id,
                'material' => $journal->material,
                'reflection' => $journal->reflection,

                'schedule' => [
                    'subject' => $journal->teachingSchedule->subject->name,
                    'classroom' => $journal->teachingSchedule->classroom->name,
                    'session' => $journal->teachingSchedule->lessonHour->session,
                    'start_time' => $journal->teachingSchedule->lessonHour->start_time,
                    'end_time' => $journal->teachingSchedule->lessonHour->end_time,
                ],

                'attendances' => $journal->attendances
            ]
        ]);
    }

    /**
     * 3. Ambil Siswa Berdasarkan Jadwal
     */
    public function getStudentsBySchedule($scheduleId)
    {
        $schedule = TeachingSchedule::with([
            'classroom',
            'lessonHour',
            'subject'
        ])
            ->findOrFail($scheduleId);

        $today = now('Asia/Jakarta')->toDateString();

        $journal = TeachingJournal::where(
            'teaching_schedule_id',
            $scheduleId
        )
            ->whereDate('date', $today)
            ->first();

        $students = Student::where(
            'classroom_id',
            $schedule->classroom_id
        )
            ->orderBy('name', 'asc')
            ->get();

        $attendances = [];

        if ($journal) {
            $attendances = StudentAttendance::where(
                'teaching_journal_id',
                $journal->id
            )
                ->pluck('status', 'student_id')
                ->toArray();
        }

        $data = $students->map(function ($s) use ($attendances) {
            return [
                'id'     => $s->id,
                'name'   => $s->name,
                'nis'    => $s->nis,
                'status' => $attendances[$s->id] ?? '',
            ];
        });

        return response()->json([
            'success' => true,

            'schedule' => [
                'id' => $schedule->id,
                'subject' => $schedule->subject->name,
                'classroom' => $schedule->classroom->name,
                'session' => $schedule->lessonHour->session,
                'start_time' => $schedule->lessonHour->start_time,
                'end_time' => $schedule->lessonHour->end_time,
            ],

            'journal_id' => $journal?->id,
            'material' => $journal?->material ?? '',
            'data' => $data
        ]);
    }

    /**
     * 4. Simpan Presensi
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'teaching_schedule_id' => 'required|exists:teaching_schedules,id',
            'material' => 'required|string',

            'attendances' => 'required|array',

            'attendances.*.student_id' => 'required|exists:students,id',

            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        return DB::transaction(function () use ($request) {

            $today = now()->toDateString();

            // Prevent duplicate journal
            $existingJournal = TeachingJournal::where(
                'teaching_schedule_id',
                $request->teaching_schedule_id
            )
                ->whereDate('date', $today)
                ->first();

            if ($existingJournal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal hari ini sudah dibuat'
                ], 422);
            }

            // Create Journal
            $journal = TeachingJournal::create([
                'teaching_schedule_id' => $request->teaching_schedule_id,
                'date' => now(),
                'material' => $request->material,
                'reflection' => null,
            ]);

            // Attendance
            foreach ($request->attendances as $att) {

                StudentAttendance::create([
                    'teaching_journal_id' => $journal->id,
                    'student_id' => $att['student_id'],
                    'status' => $att['status'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil disimpan',
                'journal_id' => $journal->id
            ]);
        });
    }

    /**
     * 5. Update Jurnal
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'material' => 'required|string',
            'attendances' => 'required|array',
        ]);

        $journal = TeachingJournal::findOrFail($id);

        DB::transaction(function () use ($request, $journal) {

            $journal->update([
                'material' => $request->material
            ]);

            foreach ($request->attendances as $att) {

                StudentAttendance::updateOrCreate(
                    [
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $att['student_id']
                    ],
                    [
                        'status' => $att['status']
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Updated'
        ]);
    }

    /**
     * 6. Simpan Refleksi
     */
    public function storeReflection(Request $request, $journalId)
    {
        $request->validate([
            'reflection' => 'required|string|min:5',
        ]);

        $journal = TeachingJournal::findOrFail($journalId);

        $journal->update([
            'reflection' => $request->reflection
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan refleksi berhasil disimpan',
            'data' => $journal
        ]);
    }
}
