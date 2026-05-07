<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleAttendanceTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class RoleAttendanceTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.role-attendance-times.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $roleAttendanceTimes = RoleAttendanceTime::with(['role', 'attendanceTimeSetting'])
                ->select('role_attendance_times.*');

            return DataTables::of($roleAttendanceTimes)
                ->addIndexColumn()
                ->addColumn('role_name', function ($row) {
                    return '<span class="badge bg-primary">' . strtoupper($row->role->name) . '</span>';
                })
                ->addColumn('attendance_name', function ($row) {
                    return $row->attendanceTimeSetting->name;
                })
                ->addColumn('check_in_time', function ($row) {
                    $setting = $row->attendanceTimeSetting;
                    $grace = $setting->grace_period_minutes > 0 ? ' (Grace period: ' . $setting->grace_period_minutes . ' menit)' : '';
                    return $setting->check_in_start . ' - ' . $setting->check_in_end . $grace;
                })
                ->addColumn('check_out_time', function ($row) {
                    $setting = $row->attendanceTimeSetting;
                    return $setting->check_out_start . ' - ' . $setting->check_out_end;
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editRoleAttendanceTime(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->role->name) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['role_name', 'aksi'])
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
            Log::info('Store role attendance time request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'role_id' => 'required|exists:roles,id',
                'attendance_time_settings_id' => 'required|exists:attendance_time_settings,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Cek duplikasi role_id - validasi role_id tidak boleh duplikat
            $exists = RoleAttendanceTime::where('role_id', $request->role_id)->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Role sudah memiliki waktu presensi!',
                    'errors' => [
                        'role_id' => ['Role ini sudah memiliki pengaturan waktu presensi. Setiap role hanya boleh memiliki satu pengaturan waktu presensi.']
                    ]
                ], 422);
            }

            $roleAttendanceTime = RoleAttendanceTime::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data waktu presensi role berhasil ditambahkan!',
                'data' => $roleAttendanceTime
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing role attendance time: ' . $e->getMessage());

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
        $roleAttendanceTime = RoleAttendanceTime::findOrFail($id);
        return response()->json($roleAttendanceTime);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Update role attendance time request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $roleAttendanceTime = RoleAttendanceTime::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'role_id' => 'required|exists:roles,id',
                'attendance_time_settings_id' => 'required|exists:attendance_time_settings,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Cek duplikasi role_id (kecuali untuk record yang sama)
            $exists = RoleAttendanceTime::where('role_id', $request->role_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Role sudah memiliki waktu presensi!',
                    'errors' => [
                        'role_id' => ['Role ini sudah memiliki pengaturan waktu presensi. Setiap role hanya boleh memiliki satu pengaturan waktu presensi.']
                    ]
                ], 422);
            }

            $roleAttendanceTime->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data waktu presensi role berhasil diupdate!',
                'data' => $roleAttendanceTime
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating role attendance time: ' . $e->getMessage());

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
            $roleAttendanceTime = RoleAttendanceTime::findOrFail($id);
            $roleAttendanceTime->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data waktu presensi role berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting role attendance time: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
