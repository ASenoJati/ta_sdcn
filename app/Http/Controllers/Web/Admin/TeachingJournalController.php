<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachingJournal;
use App\Models\JournalMaterial;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class TeachingJournalController extends Controller
{
    public function index()
    {
        return view('admin.teaching-journals.index');
    }

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
                ->addColumn('materials_count', function ($row) {
                    $count = $row->materials()->count();
                    return '<span class="badge bg-info">' . $count . ' materi</span>';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->translatedFormat('d F Y H:i');
                })
                ->addColumn('aksi', function ($row) {
                    return '
                        <a href="' . route('teaching-journals.show', $row->id) . '" class="btn btn-info btn-sm me-1">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(' . $row->id . ', \'' . addslashes($row->id) . '\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['schedule_info', 'date_info', 'attendance_summary', 'materials_count', 'aksi'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $journal = TeachingJournal::with([
            'teachingSchedule.teacher',
            'teachingSchedule.subject',
            'teachingSchedule.classroom',
            'teachingSchedule.lessonHour',
            'attendances.student',
            'materials.students',
        ])->findOrFail($id);

        // Ambil semua siswa di kelas yang sama
        $classroomStudents = Student::where('classroom_id', $journal->teachingSchedule->classroom_id)
            ->orderBy('name')
            ->get();

        return view('admin.teaching-journals.show', compact('journal', 'classroomStudents'));
    }

    // Method untuk menyimpan materi (via AJAX)
    public function storeMaterial(Request $request, $journalId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:file,link',
            'file' => 'required_if:type,file|file|max:10240', // 10MB
            'url' => 'required_if:type,link|url|max:500',
            'description' => 'nullable|string',
            'student_option' => 'required|in:all,selected',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $journal = TeachingJournal::findOrFail($journalId);

        $data = [
            'teaching_journal_id' => $journal->id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
        ];

        if ($request->type === 'file') {
            $path = $request->file('file')->store('journal-materials', 'public');
            $data['file_path'] = $path;
        } else {
            $data['url'] = $request->url;
        }

        $material = JournalMaterial::create($data);

        // Jika pilih siswa tertentu
        if ($request->student_option === 'selected' && $request->has('student_ids')) {
            $material->students()->attach($request->student_ids);
        }

        return redirect()->back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function destroyMaterial($journalId, $materialId)
    {
        try {
            // Cari jurnal
            $journal = TeachingJournal::findOrFail($journalId);

            // Cari materi dan pastikan milik jurnal ini
            $material = JournalMaterial::where('id', $materialId)
                ->where('teaching_journal_id', $journalId)
                ->firstOrFail();

            // Hapus file jika ada
            if ($material->type === 'file' && $material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            // Detach relasi siswa
            $material->students()->detach();

            // Hapus materi
            $material->delete();

            return redirect()->back()->with('success', 'Materi berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting material: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    public function getMaterialStudents($id)
    {
        $material = JournalMaterial::with('students')->findOrFail($id);
        return response()->json($material->students);
    }

    public function getStudentsList($id)
    {
        $journal = TeachingJournal::findOrFail($id);
        $students = Student::where('classroom_id', $journal->teachingSchedule->classroom_id)
            ->select('id', 'name', 'nis')
            ->orderBy('name')
            ->get();
        return response()->json($students);
    }

    public function destroy($id)
    {
        try {
            $journal = TeachingJournal::findOrFail($id);

            // Hapus semua materi dan file terkait
            foreach ($journal->materials as $material) {
                if ($material->type === 'file' && $material->path) {
                    Storage::disk('public')->delete($material->path);
                }
                $material->students()->detach();
                $material->delete();
            }

            $journal->attendances()->delete();
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
