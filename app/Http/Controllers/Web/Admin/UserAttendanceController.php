<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class UserAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.user-attendances.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $attendances = UserAttendance::with(['user', 'location'])
                ->select('user_attendances.*');

            return DataTables::of($attendances)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user ? $row->user->name : '-';
                })
                ->addColumn('location_name', function ($row) {
                    return $row->location ? $row->location->name : '-';
                })
                ->addColumn('attendance_info', function ($row) {
                    return '<div>
                        <strong>' . $row->attendance_date_formatted . '</strong><br>
                        <small>Check-in: ' . $row->check_in_time_formatted . '</small><br>
                        <small>Check-out: ' . $row->check_out_time_formatted . '</small>
                    </div>';
                })
                ->addColumn('status_info', function ($row) {
                    return '<div>
                        Check-in: ' . $row->check_in_status_badge . '<br>
                        Check-out: ' . $row->check_out_status_badge . '
                    </div>';
                })
                ->addColumn('duration', function ($row) {
                    return '<span class="badge bg-primary">' . $row->work_duration . '</span>';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button type="button" class="btn btn-info btn-sm me-1" onclick="viewDetail(' . $row->id . ')">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->user->name) . ' - ' . $row->attendance_date_formatted . '\')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['attendance_info', 'status_info', 'duration', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource with details.
     */
    public function show($id)
    {
        $attendance = UserAttendance::with(['user', 'location'])
            ->findOrFail($id);

        return response()->json($attendance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $attendance = UserAttendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data presensi guru berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user attendance: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
