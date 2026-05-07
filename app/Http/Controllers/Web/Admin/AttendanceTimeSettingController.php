<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceTimeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class AttendanceTimeSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.attendance-time-settings.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $settings = AttendanceTimeSetting::query();

            return DataTables::of($settings)
                ->addIndexColumn()
                ->addColumn('check_in_range', function ($row) {
                    return '<span class="badge bg-info">' . $row->check_in_start . '</span> - <span class="badge bg-success">' . $row->check_in_end . '</span>';
                })
                ->addColumn('check_out_range', function ($row) {
                    if ($row->check_out_start && $row->check_out_end) {
                        return '<span class="badge bg-warning">' . $row->check_out_start . '</span> - <span class="badge bg-danger">' . $row->check_out_end . '</span>';
                    }
                    return '<span class="badge bg-secondary">Tidak Diatur</span>';
                })
                ->addColumn('grace_period_formatted', function ($row) {
                    if ($row->grace_period_minutes > 0) {
                        return '<span class="badge bg-primary">' . $row->grace_period_minutes . ' menit</span>';
                    }
                    return '<span class="badge bg-secondary">0 menit</span>';
                })
                ->addColumn('usage_count', function ($row) {
                    $count = $row->roleAttendanceTimes()->count();
                    if ($count > 0) {
                        return '<span class="badge bg-info">Digunakan oleh ' . $count . ' role</span>';
                    }
                    return '<span class="badge bg-secondary">Belum digunakan</span>';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    $usedText = $row->isUsed() ? 'disabled="disabled" title="Tidak dapat dihapus karena sedang digunakan"' : '';
                    return '
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editSetting(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->name) . '\')" ' . $usedText . '>
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['check_in_range', 'check_out_range', 'grace_period_formatted', 'usage_count', 'aksi'])
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
            Log::info('Store attendance time setting request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:attendance_time_settings,name',
                'check_in_start' => 'required|date_format:H:i',
                'check_in_end' => 'required|date_format:H:i|after:check_in_start',
                'check_out_start' => 'nullable|date_format:H:i',
                'check_out_end' => 'nullable|date_format:H:i|after:check_out_start',
                'grace_period_minutes' => 'nullable|integer|min:0|max:999'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            $data['grace_period_minutes'] = $request->grace_period_minutes ?? 0;

            $setting = AttendanceTimeSetting::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Setting waktu presensi berhasil ditambahkan!',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing attendance time setting: ' . $e->getMessage());

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
    public function edit($id)
    {
        $setting = AttendanceTimeSetting::findOrFail($id);
        return response()->json($setting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Update attendance time setting request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $setting = AttendanceTimeSetting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:attendance_time_settings,name,' . $id,
                'check_in_start' => 'required|date_format:H:i',
                'check_in_end' => 'required|date_format:H:i|after:check_in_start',
                'check_out_start' => 'nullable|date_format:H:i',
                'check_out_end' => 'nullable|date_format:H:i|after:check_out_start',
                'grace_period_minutes' => 'nullable|integer|min:0|max:999'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            $data['grace_period_minutes'] = $request->grace_period_minutes ?? 0;

            $setting->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Setting waktu presensi berhasil diupdate!',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating attendance time setting: ' . $e->getMessage());

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
            $setting = AttendanceTimeSetting::findOrFail($id);

            // Cek apakah setting sedang digunakan
            if ($setting->isUsed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting waktu presensi tidak dapat dihapus karena sedang digunakan oleh role!'
                ], 400);
            }

            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Setting waktu presensi berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting attendance time setting: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of attendance time settings for dropdown
     */
    public function getList()
    {
        $settings = AttendanceTimeSetting::select('id', 'name', 'check_in_start', 'check_in_end', 'check_out_start', 'check_out_end', 'grace_period_minutes')
            ->orderBy('name')
            ->get();
        return response()->json($settings);
    }
}
