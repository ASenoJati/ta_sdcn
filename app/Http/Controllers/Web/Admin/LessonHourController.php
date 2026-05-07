<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class LessonHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.lesson-hours.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $lessonHours = LessonHour::query();

            return DataTables::of($lessonHours)
                ->addIndexColumn()
                ->addColumn('session_formatted', function ($row) {
                    return '<span class="badge bg-primary">Jam ke-' . $row->session . '</span>';
                })
                ->addColumn('time_range', function ($row) {
                    return '<span class="badge bg-info">' . $row->start_time . '</span> - <span class="badge bg-success">' . $row->end_time . '</span>';
                })
                ->addColumn('duration', function ($row) {
                    $start = strtotime($row->start_time);
                    $end = strtotime($row->end_time);
                    $diff = ($end - $start) / 60; // in minutes

                    if ($diff >= 60) {
                        $hours = floor($diff / 60);
                        $minutes = $diff % 60;
                        if ($minutes > 0) {
                            return $hours . ' jam ' . $minutes . ' menit';
                        }
                        return $hours . ' jam';
                    }
                    return $diff . ' menit';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button type="button" class="btn btn-warning btn-sm me-1" onclick="editLessonHour(' . $row->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes('Jam ke-' . $row->session) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['session_formatted', 'time_range', 'duration', 'aksi'])
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
            Log::info('Store lesson hour request', ['data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                'session' => 'required|integer|min:1|max:20|unique:lesson_hours,session',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Check if combination of start_time and end_time already exists
            $exists = LessonHour::where('start_time', $request->start_time)
                ->where('end_time', $request->end_time)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => [
                        'start_time' => ['Kombinasi waktu mulai dan selesai sudah ada!'],
                        'end_time' => ['Kombinasi waktu mulai dan selesai sudah ada!']
                    ]
                ], 422);
            }

            $lessonHour = LessonHour::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data jam pembelajaran berhasil ditambahkan!',
                'data' => $lessonHour
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing lesson hour: ' . $e->getMessage());

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
        $lessonHour = LessonHour::findOrFail($id);
        return response()->json($lessonHour);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Update lesson hour request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $lessonHour = LessonHour::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'session' => 'required|integer|min:1|max:20|unique:lesson_hours,session,' . $id,
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Check if combination of start_time and end_time already exists (excluding current record)
            $exists = LessonHour::where('start_time', $request->start_time)
                ->where('end_time', $request->end_time)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => [
                        'start_time' => ['Kombinasi waktu mulai dan selesai sudah ada!'],
                        'end_time' => ['Kombinasi waktu mulai dan selesai sudah ada!']
                    ]
                ], 422);
            }

            $lessonHour->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data jam pembelajaran berhasil diupdate!',
                'data' => $lessonHour
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating lesson hour: ' . $e->getMessage());

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
            $lessonHour = LessonHour::findOrFail($id);
            $lessonHour->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jam pembelajaran berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting lesson hour: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of lesson hours for dropdown
     */
    public function getList()
    {
        $lessonHours = LessonHour::select('id', 'session', 'start_time', 'end_time')
            ->orderBy('session')
            ->get();
        return response()->json($lessonHours);
    }
}
