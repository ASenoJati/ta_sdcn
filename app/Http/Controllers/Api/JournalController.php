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
     * Helper: Kelompokkan jadwal berdasarkan subject, classroom, dan blok jam berurutan
     */
    private function groupSchedules($schedules)
    {
        // Urutkan berdasarkan lesson_hour_id
        $sorted = $schedules->sortBy('lesson_hour_id')->values();

        $groups = [];
        $currentGroup = null;

        foreach ($sorted as $schedule) {
            $subjectId = $schedule->subject_id;
            $classroomId = $schedule->classroom_id;
            $session = $schedule->lessonHour->session;

            if ($currentGroup === null) {
                // Mulai group baru
                $currentGroup = [
                    'subject_id' => $subjectId,
                    'classroom_id' => $classroomId,
                    'schedules' => collect([$schedule]),
                    'last_session' => $session,
                ];
                continue;
            }

            // Cek apakah subject dan classroom sama, serta session berurutan
            if (
                $currentGroup['subject_id'] == $subjectId &&
                $currentGroup['classroom_id'] == $classroomId &&
                $session == $currentGroup['last_session'] + 1
            ) {
                // Masukkan ke group yang sama
                $currentGroup['schedules']->push($schedule);
                $currentGroup['last_session'] = $session;
            } else {
                // Simpan group sebelumnya, mulai group baru
                $groups[] = $currentGroup;
                $currentGroup = [
                    'subject_id' => $subjectId,
                    'classroom_id' => $classroomId,
                    'schedules' => collect([$schedule]),
                    'last_session' => $session,
                ];
            }
        }

        if ($currentGroup !== null) {
            $groups[] = $currentGroup;
        }

        return $groups;
    }

    /**
     * 1. Jadwal Mengajar Hari Ini (dikelompokkan per blok pertemuan)
     */
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $dayName = now()->format('l');

        $schedules = TeachingSchedule::with(['subject', 'classroom', 'lessonHour'])
            ->where('user_id', $request->user()->id)
            ->where('day', $dayName)
            ->orderBy('lesson_hour_id')
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Kelompokkan jadwal per blok pertemuan
        $groups = $this->groupSchedules($schedules);

        $result = [];
        foreach ($groups as $group) {
            $firstSchedule = $group['schedules']->first();
            $scheduleIds = $group['schedules']->pluck('id')->toArray();

            // Cek apakah sudah ada jurnal untuk blok ini (hari ini)
            $journalExists = TeachingJournal::where('user_id', $request->user()->id)
                ->where('subject_id', $group['subject_id'])
                ->where('classroom_id', $group['classroom_id'])
                ->whereDate('date', $today)
                ->whereHas('schedules', function ($q) use ($scheduleIds) {
                    $q->whereIn('teaching_schedule_id', $scheduleIds);
                })
                ->exists();

            // Ambil semua lesson_hour dari schedules di group
            $lessonHours = $group['schedules']->map(function ($s) {
                return [
                    'id' => $s->lessonHour->id,
                    'session' => $s->lessonHour->session,
                    'start_time' => $s->lessonHour->start_time,
                    'end_time' => $s->lessonHour->end_time,
                ];
            })->values();

            $result[] = [
                // Gunakan ID schedule pertama sebagai representasi
                'id' => $firstSchedule->id,
                'subject' => $firstSchedule->subject ? [
                    'id' => $firstSchedule->subject->id,
                    'name' => $firstSchedule->subject->name,
                ] : null,
                'classroom' => $firstSchedule->classroom ? [
                    'id' => $firstSchedule->classroom->id,
                    'name' => $firstSchedule->classroom->name,
                ] : null,
                'lesson_hours' => $lessonHours, // array semua jam dalam blok
                'day' => $firstSchedule->day,
                'is_journal_filled' => $journalExists,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * 2. Detail Jurnal (berdasarkan schedule_id) - tetap pakai schedule_id, cari jurnal dari grouping
     */
    public function detail($scheduleId)
    {
        $schedule = TeachingSchedule::with(['subject', 'classroom', 'lessonHour'])->find($scheduleId);
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }

        $today = now()->toDateString();
        $user = request()->user();

        // Cari jurnal berdasarkan grouping
        $journal = TeachingJournal::where('user_id', $user->id)
            ->where('subject_id', $schedule->subject_id)
            ->where('classroom_id', $schedule->classroom_id)
            ->whereDate('date', $today)
            ->first();

        if (!$journal) {
            return response()->json([
                'success' => true,
                'message' => 'Jurnal belum diisi hari ini',
                'data' => [
                    'id' => null,
                    'material' => '',
                    'reflection' => '',
                    'schedule' => [
                        'subject' => $schedule->subject?->name ?? '-',
                        'classroom' => $schedule->classroom?->name ?? '-',
                        'session' => $schedule->lessonHour?->session ?? '-',
                        'start_time' => $schedule->lessonHour?->start_time ?? '-',
                        'end_time' => $schedule->lessonHour?->end_time ?? '-',
                    ],
                    'attendances' => [],
                ]
            ]);
        }

        $schedules = $journal->schedules;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $journal->id,
                'material' => $journal->material,
                'reflection' => $journal->reflection,
                'schedule' => [
                    'subject' => $schedule->subject?->name ?? '-',
                    'classroom' => $schedule->classroom?->name ?? '-',
                    'session' => $schedules->pluck('lessonHour.session')->unique()->implode(', '),
                    'start_time' => $schedules->pluck('lessonHour.start_time')->min() ?? '-',
                    'end_time' => $schedules->pluck('lessonHour.end_time')->max() ?? '-',
                ],
                'attendances' => $journal->attendances->map(function ($att) {
                    return [
                        'id' => $att->id,
                        'student_id' => $att->student_id,
                        'status' => $att->status,
                        'student' => $att->student ? [
                            'id' => $att->student->id,
                            'name' => $att->student->name,
                            'nis' => $att->student->nis,
                        ] : null,
                    ];
                }),
            ]
        ]);
    }

    /**
     * 3. Ambil Siswa Berdasarkan Jadwal (tetap pakai schedule_id)
     */
    public function getStudentsBySchedule($scheduleId)
    {
        $schedule = TeachingSchedule::with(['classroom', 'lessonHour', 'subject'])->find($scheduleId);
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }

        $today = now('Asia/Jakarta')->toDateString();
        $user = request()->user();

        $journal = TeachingJournal::where('user_id', $user->id)
            ->where('subject_id', $schedule->subject_id)
            ->where('classroom_id', $schedule->classroom_id)
            ->whereDate('date', $today)
            ->first();

        $students = Student::where('classroom_id', $schedule->classroom_id)
            ->orderBy('name', 'asc')
            ->get();

        $attendances = [];
        if ($journal) {
            $attendances = StudentAttendance::where('teaching_journal_id', $journal->id)
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
                'subject' => $schedule->subject?->name ?? '-',
                'classroom' => $schedule->classroom?->name ?? '-',
                'session' => $schedule->lessonHour?->session ?? '-',
                'start_time' => $schedule->lessonHour?->start_time ?? '-',
                'end_time' => $schedule->lessonHour?->end_time ?? '-',
            ],
            'journal_id' => $journal?->id,
            'material' => $journal?->material ?? '',
            'data' => $data
        ]);
    }

    /**
     * 4. Simpan Presensi (create or update)
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

        $schedule = TeachingSchedule::find($request->teaching_schedule_id);
        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }

        $user = $request->user();
        $today = now()->toDateString();

        return DB::transaction(function () use ($request, $schedule, $user, $today) {
            // Cari atau buat jurnal berdasarkan grouping
            $journal = TeachingJournal::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'subject_id' => $schedule->subject_id,
                    'classroom_id' => $schedule->classroom_id,
                    'date' => $today,
                ],
                [
                    'material' => $request->material,
                    'reflection' => null,
                ]
            );

            // Relasikan dengan schedule (many-to-many) jika belum ada
            if (!$journal->schedules()->where('teaching_schedule_id', $schedule->id)->exists()) {
                $journal->schedules()->attach($schedule->id);
            }

            // Update material jika jurnal sudah ada
            if (!$journal->wasRecentlyCreated) {
                $journal->update(['material' => $request->material]);
            }

            // Simpan atau update presensi siswa
            foreach ($request->attendances as $att) {
                StudentAttendance::updateOrCreate(
                    [
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $att['student_id'],
                    ],
                    ['status' => $att['status']]
                );
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
            $journal->update(['material' => $request->material]);

            foreach ($request->attendances as $att) {
                StudentAttendance::updateOrCreate(
                    [
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $att['student_id'],
                    ],
                    ['status' => $att['status']]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil diperbarui'
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
        $journal->update(['reflection' => $request->reflection]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan refleksi berhasil disimpan',
            'data' => $journal
        ]);
    }

    /**
     * 7. Semua Jadwal Mingguan (dikelompokkan juga)
     */
    public function allSchedules(Request $request)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $allSchedules = TeachingSchedule::with(['subject', 'classroom', 'lessonHour'])
            ->where('user_id', $request->user()->id)
            ->orderBy('lesson_hour_id')
            ->get()
            ->groupBy('day');

        $formattedData = [];
        foreach ($days as $day) {
            if (isset($allSchedules[$day]) && $allSchedules[$day]->isNotEmpty()) {
                // Kelompokkan per blok pertemuan
                $groups = $this->groupSchedules($allSchedules[$day]);
                $formattedData[$day] = collect($groups)->map(function ($group) {
                    $first = $group['schedules']->first();
                    $lessonHours = $group['schedules']->map(function ($s) {
                        return [
                            'session' => $s->lessonHour->session,
                            'start_time' => $s->lessonHour->start_time,
                            'end_time' => $s->lessonHour->end_time,
                        ];
                    });
                    return [
                        'id' => $first->id,
                        'subject' => $first->subject?->name ?? '-',
                        'classroom' => $first->classroom?->name ?? '-',
                        'lesson_hours' => $lessonHours,
                    ];
                });
            } else {
                $formattedData[$day] = [];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal Mingguan (dikelompokkan per blok pertemuan)',
            'data' => $formattedData
        ]);
    }

    /**
     * 8. Riwayat Jurnal
     */
    public function history(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $journals = TeachingJournal::with(['schedules.subject', 'schedules.classroom', 'schedules.lessonHour'])
            ->where('user_id', $request->user()->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($journal) {
                $schedules = $journal->schedules;
                $first = $schedules->first();
                return [
                    'id' => $first?->id,
                    'journal_id' => $journal->id,
                    'date' => $journal->date->toDateString(),
                    'subject' => $first?->subject ? [
                        'id' => $first->subject->id,
                        'name' => $first->subject->name,
                    ] : null,
                    'classroom' => $first?->classroom ? [
                        'id' => $first->classroom->id,
                        'name' => $first->classroom->name,
                    ] : null,
                    'lesson_hours' => $schedules->map(function ($s) {
                        return [
                            'session' => $s->lessonHour->session,
                            'start_time' => $s->lessonHour->start_time,
                            'end_time' => $s->lessonHour->end_time,
                        ];
                    }),
                    'day' => $first?->day ?? '-',
                    'is_journal_filled' => true,
                ];
            });

        return response()->json([
            'success' => true,
            'filter' => ['month' => (int)$month, 'year' => (int)$year],
            'data' => $journals
        ]);
    }

    /**
     * 9. Detail Jurnal Berdasarkan ID
     */
    public function getJournalById($journalId)
    {
        $journal = TeachingJournal::with([
            'attendances.student',
            'schedules.lessonHour',
            'schedules.subject',
            'schedules.classroom'
        ])
            ->where('user_id', request()->user()->id)
            ->find($journalId);

        if (!$journal) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal tidak ditemukan'
            ], 404);
        }

        $schedules = $journal->schedules;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $journal->id,
                'material' => $journal->material,
                'reflection' => $journal->reflection,
                'schedule' => [
                    'subject' => $schedules->first()?->subject?->name ?? '-',
                    'classroom' => $schedules->first()?->classroom?->name ?? '-',
                    'session' => $schedules->pluck('lessonHour.session')->unique()->implode(', '),
                    'start_time' => $schedules->pluck('lessonHour.start_time')->min() ?? '-',
                    'end_time' => $schedules->pluck('lessonHour.end_time')->max() ?? '-',
                ],
                'attendances' => $journal->attendances->map(function ($att) {
                    return [
                        'id' => $att->id,
                        'student_id' => $att->student_id,
                        'status' => $att->status,
                        'created_at' => $att->created_at,
                        'updated_at' => $att->updated_at,
                        'student' => $att->student ? [
                            'id' => $att->student->id,
                            'classroom_id' => $att->student->classroom_id,
                            'name' => $att->student->name,
                            'nis' => $att->student->nis,
                        ] : null,
                    ];
                }),
            ]
        ]);
    }
}
