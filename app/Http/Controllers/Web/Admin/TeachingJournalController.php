<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class TeachingJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.teaching-journals.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $journals = TeachingJournal::with(['teachingSchedule.teacher', 'teachingSchedule.subject', 'teachingSchedule.classroom', 'teachingSchedule.lessonHour'])
                ->select('teaching_journals.*');

            return DataTables::of($journals)
                ->addIndexColumn()
                ->addColumn('schedule_info', function ($row) {
                    $schedule = $row->teachingSchedule;
                    return '<div>
                        <strong>' . $schedule->subject->name . '</strong><br>
                        <small>Kelas: ' . $schedule->classroom->name . '</small><br>
                        <small>Guru: ' . ($schedule->teacher ? $schedule->teacher->name : '-') . '</small>
                    </div>';
                })
                ->addColumn('date_info', function ($row) {
                    return '<div>
                        <strong>' . $row->day_name . '</strong><br>
                        <small>' . $row->date_formatted . '</small>
                    </div>';
                })
                ->addColumn('material_preview', function ($row) {
                    return \Str::limit($row->material, 50);
                })
                ->addColumn('attendance_summary', function ($row) {
                    $summary = $row->attendance_summary;
                    return '<div class="text-center">
                        <span class="badge bg-success">H: ' . $summary['hadir'] . '</span>
                        <span class="badge bg-warning">I: ' . $summary['izin'] . '</span>
                        <span class="badge bg-info">S: ' . $summary['sakit'] . '</span>
                        <span class="badge bg-danger">A: ' . $summary['alpa'] . '</span>
                        <br><small>Total: ' . $summary['total'] . ' siswa</small>
                    </div>';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <button type="button" class="btn btn-info btn-sm me-1" onclick="viewDetail(' . $row->id . ')">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->teachingSchedule->subject->name) . ' - ' . $row->date_formatted . '\')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['schedule_info', 'date_info', 'attendance_summary', 'aksi'])
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
        $journal = TeachingJournal::with([
            'teachingSchedule.teacher',
            'teachingSchedule.subject',
            'teachingSchedule.classroom',
            'teachingSchedule.lessonHour',
            'attendances.student'
        ])->findOrFail($id);

        return response()->json($journal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $journal = TeachingJournal::findOrFail($id);

            // Delete related student attendances first
            $journal->attendances()->delete();

            // Delete the journal
            $journal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jurnal pembelajaran berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting teaching journal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
