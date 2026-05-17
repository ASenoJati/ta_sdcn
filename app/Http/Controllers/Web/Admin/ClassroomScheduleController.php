<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\TeachingSchedule;
use App\Models\User;
use App\Models\LessonHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class ClassroomScheduleController extends Controller
{
    /**
     * Display a listing of classrooms.
     */
    public function index()
    {
        return view('admin.classroom-schedules.index');
    }

    /**
     * Get data for DataTables (list of classrooms).
     */
    public function getData(Request $request)
    {
        try {
            $classrooms = Classroom::withCount('schedules')->orderBy('name');

            return DataTables::of($classrooms)
                ->addIndexColumn()
                ->addColumn('schedule_count', function ($row) {
                    $count = $row->schedules_count;
                    $color = $count > 0 ? 'success' : 'warning';
                    return '<span class="badge bg-' . $color . '">' . $count . ' Jadwal</span>';
                })
                ->addColumn('description_short', function ($row) {
                    return \Str::limit($row->description, 50) ?? '-';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <a href="' . route('classroom-schedules.show', $row->id) . '" class="btn btn-info btn-sm me-1">
                            <i class="bi bi-eye"></i> Lihat Jadwal
                        </a>
                    ';
                })
                ->rawColumns(['schedule_count', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display schedule for specific classroom.
     */
    public function show($id)
    {
        $classroom = Classroom::findOrFail($id);
        return view('admin.classroom-schedules.show', compact('classroom'));
    }

    /**
     * Get schedule data for specific classroom.
     */
    public function getScheduleData(Request $request, $id)
    {
        try {
            $schedules = TeachingSchedule::with(['teacher', 'subject', 'lessonHour'])
                ->where('classroom_id', $id)
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                ->orderBy('lesson_hour_id');

            return DataTables::of($schedules)
                ->addIndexColumn()
                ->addColumn('teacher_name', function ($row) {
                    return $row->teacher ? $row->teacher->name : '-';
                })
                ->addColumn('subject_name', function ($row) {
                    return '<span class="badge bg-primary">' . $row->subject->name . '</span>';
                })
                ->addColumn('day_indonesian', function ($row) {
                    $dayColors = [
                        'Monday' => 'primary',
                        'Tuesday' => 'success',
                        'Wednesday' => 'warning',
                        'Thursday' => 'info',
                        'Friday' => 'danger',
                        'Saturday' => 'dark',
                        'Sunday' => 'secondary'
                    ];
                    return '<span class="badge bg-' . $dayColors[$row->day] . '">' . $row->day_indonesian . '</span>';
                })
                ->addColumn('lesson_time', function ($row) {
                    if ($row->lessonHour) {
                        return 'Jam ke-' . $row->lessonHour->session . ' (' . $row->lessonHour->start_time . ' - ' . $row->lessonHour->end_time . ')';
                    }
                    return '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editSchedule(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->schedule_info) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['subject_name', 'day_indonesian', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created schedule.
     */
    public function storeSchedule(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'subject_id' => 'required|exists:subjects,id',
                'classroom_id' => 'required|exists:classrooms,id',
                'lesson_hour_id' => 'required|exists:lesson_hours,id',
                'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Validasi konflik
            $classroomConflict = TeachingSchedule::where('classroom_id', $request->classroom_id)
                ->where('day', $request->day)
                ->where('lesson_hour_id', $request->lesson_hour_id)
                ->exists();

            if ($classroomConflict) {
                return response()->json([
                    'success' => false,
                    'type' => 'classroom_conflict',
                    'message' => 'Jadwal bentrok! Kelas ini sudah memiliki mata pelajaran pada hari dan jam yang sama.'
                ], 422);
            }

            $teacherConflict = TeachingSchedule::where('user_id', $request->user_id)
                ->where('day', $request->day)
                ->where('lesson_hour_id', $request->lesson_hour_id)
                ->exists();

            if ($teacherConflict) {
                return response()->json([
                    'success' => false,
                    'type' => 'teacher_conflict',
                    'message' => 'Jadwal bentrok! Guru ini sudah mengajar pada hari dan jam yang sama.'
                ], 422);
            }

            $schedule = TeachingSchedule::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal pembelajaran berhasil ditambahkan!',
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing teaching schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update schedule.
     */
    public function updateSchedule(Request $request, $id)
    {
        try {
            $schedule = TeachingSchedule::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'subject_id' => 'required|exists:subjects,id',
                'classroom_id' => 'required|exists:classrooms,id',
                'lesson_hour_id' => 'required|exists:lesson_hours,id',
                'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Validasi 1: Kelas tidak boleh memiliki 2 mapel di jam yang sama (exclude current)
            $classroomConflict = TeachingSchedule::where('classroom_id', $request->classroom_id)
                ->where('day', $request->day)
                ->where('lesson_hour_id', $request->lesson_hour_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($classroomConflict) {
                return response()->json([
                    'success' => false,
                    'type' => 'classroom_conflict',
                    'message' => 'Jadwal bentrok! Kelas ini sudah memiliki mata pelajaran pada hari dan jam yang sama.',
                    'errors' => [
                        'classroom_id' => ['Kelas ini sudah memiliki jadwal pada hari dan jam yang sama!'],
                        'lesson_hour_id' => ['Jam pelajaran ini sudah terisi untuk kelas ini di hari yang sama!']
                    ]
                ], 422);
            }

            // Validasi 2: Guru tidak boleh mengajar 2 kelas di jam yang sama (exclude current)
            $teacherConflict = TeachingSchedule::where('user_id', $request->user_id)
                ->where('day', $request->day)
                ->where('lesson_hour_id', $request->lesson_hour_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($teacherConflict) {
                $conflictingSchedule = TeachingSchedule::where('user_id', $request->user_id)
                    ->where('day', $request->day)
                    ->where('lesson_hour_id', $request->lesson_hour_id)
                    ->where('id', '!=', $id)
                    ->with(['classroom', 'subject'])
                    ->first();

                return response()->json([
                    'success' => false,
                    'type' => 'teacher_conflict',
                    'message' => 'Jadwal bentrok! Guru ini sudah mengajar di kelas ' . $conflictingSchedule->classroom->name . ' pada hari dan jam yang sama.',
                    'errors' => [
                        'user_id' => ['Guru ini sudah memiliki jadwal mengajar di kelas lain pada hari dan jam yang sama!'],
                        'lesson_hour_id' => ['Jam pelajaran ini sudah digunakan untuk guru ini di hari yang sama!']
                    ]
                ], 422);
            }

            $schedule->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal pembelajaran berhasil diupdate!',
                'data' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating teaching schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete schedule.
     */
    public function destroySchedule($id)
    {
        try {
            $schedule = TeachingSchedule::findOrFail($id);
            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jadwal pembelajaran berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting teaching schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schedule for edit.
     */
    public function editSchedule($id)
    {
        $schedule = TeachingSchedule::findOrFail($id);
        return response()->json($schedule);
    }

    /**
     * Get teachers list.
     */
    public function getTeachers()
    {
        $teachers = User::where('role_id', 2)->select('id', 'name')->orderBy('name')->get();
        return response()->json($teachers);
    }

    /**
     * Get lesson hours list.
     */
    public function getLessonHours()
    {
        $lessonHours = LessonHour::select('id', 'session', 'start_time', 'end_time')
            ->orderBy('session')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => 'Jam ke-' . $item->session . ' (' . $item->start_time . ' - ' . $item->end_time . ')'
                ];
            });
        return response()->json($lessonHours);
    }

    /**
     * Check schedule availability.
     */
    public function checkAvailability(Request $request)
    {
        $response = [
            'available' => true,
            'classroom_conflict' => false,
            'teacher_conflict' => false,
            'classroom_conflict_info' => null,
            'teacher_conflict_info' => null
        ];

        $classroomConflict = TeachingSchedule::where('classroom_id', $request->classroom_id)
            ->where('day', $request->day)
            ->where('lesson_hour_id', $request->lesson_hour_id)
            ->when($request->id, function ($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->with(['subject', 'teacher'])
            ->first();

        if ($classroomConflict) {
            $response['available'] = false;
            $response['classroom_conflict'] = true;
            $response['classroom_conflict_info'] = [
                'subject' => $classroomConflict->subject->name,
                'teacher' => $classroomConflict->teacher->name,
                'lesson_hour' => 'Jam ke-' . $classroomConflict->lessonHour->session
            ];
        }

        if ($request->user_id) {
            $teacherConflict = TeachingSchedule::where('user_id', $request->user_id)
                ->where('day', $request->day)
                ->where('lesson_hour_id', $request->lesson_hour_id)
                ->when($request->id, function ($query, $id) {
                    return $query->where('id', '!=', $id);
                })
                ->with(['classroom', 'subject'])
                ->first();

            if ($teacherConflict) {
                $response['available'] = false;
                $response['teacher_conflict'] = true;
                $response['teacher_conflict_info'] = [
                    'classroom' => $teacherConflict->classroom->name,
                    'subject' => $teacherConflict->subject->name,
                    'lesson_hour' => 'Jam ke-' . $teacherConflict->lessonHour->session
                ];
            }
        }

        return response()->json($response);
    }
}
