<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.classrooms.index');
    }

    /**
     * Display students by classroom.
     */
    public function showStudents($id)
    {
        $classroom = Classroom::findOrFail($id);
        return view('admin.classrooms.students', compact('classroom'));
    }

    /**
     * Get students data for specific classroom.
     */
    public function getStudentsData(Request $request, $id)
    {
        try {
            $classroom = Classroom::findOrFail($id);

            $students = Student::where('classroom_id', $id)
                ->with('classroom')
                ->select('students.*');

            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('classroom_name', function ($row) use ($classroom) {
                    return $classroom->name;
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editStudent(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $classrooms = Classroom::withCount('students');

            return DataTables::of($classrooms)
                ->addIndexColumn()
                ->addColumn('students_count', function ($row) {
                    return '<span class="badge bg-primary">' . $row->students_count . ' Siswa</span>';
                })
                ->addColumn('description_short', function ($row) {
                    return \Str::limit($row->description, 50) ?? '-';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <a href="' . route('classrooms.students', $row->id) . '" class="btn btn-info btn-sm me-1">
                            <i class="bi bi-eye"></i> Lihat Siswa
                        </a>
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editClassroom(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['students_count', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:classrooms,name',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $classroom = Classroom::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil ditambahkan!',
                'data' => $classroom
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $classroom = Classroom::findOrFail($id);
        return response()->json($classroom);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $classroom = Classroom::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:classrooms,name,' . $id,
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $classroom->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil diupdate!',
                'data' => $classroom
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $classroom = Classroom::findOrFail($id);

            if ($classroom->students()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tidak dapat dihapus karena masih memiliki siswa!'
                ], 400);
            }

            $classroom->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of classrooms for dropdown.
     */
    public function getList()
    {
        $classrooms = Classroom::select('id', 'name')->orderBy('name')->get();
        return response()->json($classrooms);
    }
}
