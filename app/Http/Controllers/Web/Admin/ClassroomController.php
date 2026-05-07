<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Exception;
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
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $classrooms = Classroom::withCount('students')->select('classrooms.*');

            return DataTables::of($classrooms)
                ->addIndexColumn()
                ->addColumn('description', function ($row) {
                    return $row->description ?? '-';
                })
                ->addColumn('students_count', function ($row) {
                    $count = $row->students_count;
                    $badgeColor = $count > 0 ? 'primary' : 'secondary';
                    return '<span class="badge bg-' . $badgeColor . '">' . $count . ' Siswa</span>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
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
     * Get list of classrooms for dropdown.
     */
    public function getList()
    {
        $classrooms = Classroom::select('id', 'name')->orderBy('name')->get();
        return response()->json($classrooms);
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
            Log::info('Store classroom request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:classrooms,name',
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
        } catch (Exception $e) {
            Log::error('Error storing classroom: ' . $e->getMessage());

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
        $classroom = Classroom::findOrFail($id);
        return response()->json($classroom);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Update classroom request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $classroom = Classroom::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:classrooms,name,' . $id,
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
            Log::error('Error updating classroom: ' . $e->getMessage());

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

            // Cek apakah kelas memiliki siswa
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
            Log::error('Error deleting classroom: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
