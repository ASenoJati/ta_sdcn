<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.students.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $students = Student::with('classroom')->select('students.*');

            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('classroom', function ($row) {
                    return $row->classroom ? $row->classroom->name : '-';
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Store student request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'nis' => 'required|string|max:20|unique:students,nis',
                'name' => 'required|string|max:100',
                'classroom_id' => 'required|exists:classrooms,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $student = Student::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data student berhasil ditambahkan!',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing student: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    /**
     * Update the specified resource in storage.
     */
    // Di StudentsController.php - ubah method update
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Log request untuk debugging
            Log::info('Update student request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            // Cari student
            $student = Student::findOrFail($id);

            // Validasi
            $validator = Validator::make($request->all(), [
                'nis' => 'required|string|max:20|unique:students,nis,' . $id,
                'name' => 'required|string|max:100',
                'classroom_id' => 'required|exists:classrooms,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Update data
            $student->update([
                'nis' => $request->nis,
                'name' => $request->name,
                'classroom_id' => $request->classroom_id
            ]);

            // Log success
            Log::info('Student updated successfully', ['id' => $student->id]);

            return response()->json([
                'success' => true,
                'message' => 'Data student berhasil diupdate!',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error updating student: ' . $e->getMessage());

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
            $student = Student::findOrFail($id);
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data student berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
