<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.academic-years.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $academicYears = AcademicYear::query();

            return DataTables::of($academicYears)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>';
                })
                ->addColumn('period', function ($row) {
                    return $row->start_date->format('d/m/Y') . ' - ' . $row->end_date->format('d/m/Y');
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->translatedFormat('d F Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    $buttons = '';
                    if (!$row->is_active) {
                        $buttons .= '<button type="button" class="btn btn-success btn-sm me-1" onclick="setActive(' . $row->id . ')">
                                        <i class="bi bi-check-circle"></i> Aktifkan
                                    </button>';
                    }
                    $buttons .= '
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editAcademicYear(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                    return $buttons;
                })
                ->rawColumns(['status', 'aksi'])
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
                'name' => 'required|string|max:255|unique:academic_years,name',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ], [
                'name.required' => 'Nama tahun ajaran wajib diisi.',
                'name.unique' => 'Nama tahun ajaran sudah digunakan.',
                'start_date.required' => 'Tanggal mulai wajib diisi.',
                'end_date.required' => 'Tanggal selesai wajib diisi.',
                'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $academicYear = AcademicYear::create([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => false, // Default tidak aktif
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil ditambahkan!',
                'data' => $academicYear
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing academic year: ' . $e->getMessage());
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
        $academicYear = AcademicYear::findOrFail($id);
        return response()->json($academicYear);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $academicYear = AcademicYear::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:academic_years,name,' . $id,
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ], [
                'name.required' => 'Nama tahun ajaran wajib diisi.',
                'name.unique' => 'Nama tahun ajaran sudah digunakan.',
                'start_date.required' => 'Tanggal mulai wajib diisi.',
                'end_date.required' => 'Tanggal selesai wajib diisi.',
                'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $academicYear->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil diupdate!',
                'data' => $academicYear
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating academic year: ' . $e->getMessage());
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
            $academicYear = AcademicYear::findOrFail($id);

            // Cek apakah tahun ajaran sedang aktif
            if ($academicYear->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun ajaran aktif tidak dapat dihapus! Nonaktifkan terlebih dahulu.'
                ], 400);
            }

            // Cek apakah ada relasi yang menggunakan tahun ajaran ini
            $relatedCount = 0;
            $relatedCount += $academicYear->teachingSchedules()->count();
            $relatedCount += $academicYear->teachingJournals()->count();
            $relatedCount += $academicYear->students()->count();
            $relatedCount += $academicYear->classrooms()->count();
            $relatedCount += $academicYear->subjects()->count();
            $relatedCount += $academicYear->lessonHours()->count();
            $relatedCount += $academicYear->locations()->count();
            $relatedCount += $academicYear->userAttendances()->count();
            $relatedCount += $academicYear->studentAttendances()->count();

            if ($relatedCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun ajaran tidak dapat dihapus karena masih digunakan oleh ' . $relatedCount . ' data terkait!'
                ], 400);
            }

            $academicYear->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting academic year: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set academic year as active.
     */
    public function setActive($id)
    {
        try {
            // Nonaktifkan semua tahun ajaran
            AcademicYear::where('is_active', true)->update(['is_active' => false]);

            // Aktifkan tahun ajaran yang dipilih
            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->is_active = true;
            $academicYear->save();

            // Simpan ke session untuk filter default
            session(['academic_year_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran "' . $academicYear->name . '" berhasil diaktifkan!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting active academic year: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of academic years for dropdown.
     */
    public function getList()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return response()->json($academicYears);
    }
}
