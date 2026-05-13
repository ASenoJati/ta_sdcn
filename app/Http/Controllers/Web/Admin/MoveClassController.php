<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoveClassController extends Controller
{
    /**
     * Display the move class form.
     */
    public function index()
    {
        $classrooms = Classroom::orderBy('name')->get();
        return view('admin.move-class.index', compact('classrooms'));
    }

    /**
     * Get students by classroom via AJAX.
     */
    public function getStudents(Request $request)
    {
        $fromClassroomId = $request->from_classroom_id;
        $toClassroomId = $request->to_classroom_id;

        $response = [
            'from_students' => [],
            'to_students' => [],
            'from_count' => 0,
            'to_count' => 0
        ];

        if ($fromClassroomId) {
            $fromStudents = Student::where('classroom_id', $fromClassroomId)
                ->orderBy('name')
                ->get(['id', 'nis', 'name']);
            $response['from_students'] = $fromStudents;
            $response['from_count'] = $fromStudents->count();
        }

        if ($toClassroomId) {
            $toStudents = Student::where('classroom_id', $toClassroomId)
                ->orderBy('name')
                ->get(['id', 'nis', 'name']);
            $response['to_students'] = $toStudents;
            $response['to_count'] = $toStudents->count();
        }

        return response()->json($response);
    }

    /**
     * Process move students to new classroom.
     */
    public function moveStudents(Request $request)
    {
        try {
            $request->validate([
                'from_classroom_id' => 'required|exists:classrooms,id',
                'to_classroom_id' => 'required|exists:classrooms,id|different:from_classroom_id',
                'student_ids' => 'required|array|min:1',
                'student_ids.*' => 'exists:students,id'
            ], [
                'from_classroom_id.required' => 'Pilih kelas asal terlebih dahulu',
                'to_classroom_id.required' => 'Pilih kelas tujuan',
                'to_classroom_id.different' => 'Kelas tujuan harus berbeda dari kelas asal',
                'student_ids.required' => 'Pilih minimal satu siswa untuk dipindahkan',
                'student_ids.min' => 'Pilih minimal satu siswa untuk dipindahkan'
            ]);

            $fromClassroom = Classroom::find($request->from_classroom_id);
            $toClassroom = Classroom::find($request->to_classroom_id);
            $studentCount = count($request->student_ids);

            DB::beginTransaction();

            // Update students classroom
            Student::whereIn('id', $request->student_ids)
                ->update(['classroom_id' => $request->to_classroom_id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil memindahkan {$studentCount} siswa dari kelas {$fromClassroom->name} ke kelas {$toClassroom->name}",
                'moved_count' => $studentCount
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Move class error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
