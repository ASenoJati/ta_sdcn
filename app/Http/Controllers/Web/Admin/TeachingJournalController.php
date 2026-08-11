<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeachingJournal;
use App\Models\JournalMaterial;
use App\Models\Student;
use App\Models\StudentAttendance;
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
            $journals = TeachingJournal::with(['schedules.subject', 'schedules.classroom', 'schedules.teacher', 'schedules.lessonHour'])
                ->select('teaching_journals.*');

            return DataTables::of($journals)
                ->addIndexColumn()
                ->addColumn('schedule_info', function ($row) {
                    $first = $row->schedules->first();
                    if (!$first) {
                        return '<div><span class="text-danger">Jadwal tidak ditemukan</span></div>';
                    }
                    $subjectName = $first->subject ? $first->subject->name : '-';
                    $className = $first->classroom ? $first->classroom->name : '-';
                    $teacherName = $first->teacher ? $first->teacher->name : '-';
                    $sessions = $row->schedules->pluck('lessonHour.session')->unique()->implode(', ');
                    return '<div>
                    <strong>' . $subjectName . '</strong><br>
                    <small>Kelas: ' . $className . '</small><br>
                    <small>Guru: ' . $teacherName . '</small><br>
                    <small>Jam: ' . $sessions . '</small>
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
            'schedules.teacher',
            'schedules.subject',
            'schedules.classroom',
            'schedules.lessonHour',
            'attendances.student',
            'materials.students',
        ])->findOrFail($id);

        // Ambil jadwal pertama (semua jadwal dalam blok memiliki subject, classroom, teacher yang sama)
        $firstSchedule = $journal->schedules->first();
        if (!$firstSchedule) {
            return redirect()->route('teaching-journals.index')
                ->with('error', 'Jurnal ini tidak memiliki jadwal yang valid.');
        }

        // Ambil siswa dari kelas yang sama
        $classroomStudents = Student::where('classroom_id', $firstSchedule->classroom_id)
            ->orderBy('name')
            ->get();

        // Filter siswa tidak hadir (izin, sakit, alpa)
        $filteredAttendances = $journal->attendances->filter(function ($attendance) {
            return in_array($attendance->status, ['izin', 'sakit', 'alpa']);
        });

        return view('admin.teaching-journals.show', compact(
            'journal',
            'firstSchedule',
            'classroomStudents',
            'filteredAttendances'
        ));
    }

    /**
     * Menambahkan siswa tidak hadir (izin, sakit, alpa) ke jurnal
     */
    public function addAbsentStudent(Request $request, $journalId)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:izin,sakit,alpa',
        ]);

        $journal = TeachingJournal::findOrFail($journalId);

        // Cek apakah siswa sudah ada di jurnal ini
        $existing = StudentAttendance::where('teaching_journal_id', $journal->id)
            ->where('student_id', $request->student_id)
            ->first();

        if ($existing) {
            // Jika sudah ada, update statusnya
            $existing->update(['status' => $request->status]);
            $message = 'Status siswa berhasil diperbarui menjadi ' . $request->status;
        } else {
            // Tambah baru
            StudentAttendance::create([
                'teaching_journal_id' => $journal->id,
                'student_id' => $request->student_id,
                'status' => $request->status,
            ]);
            $message = 'Siswa berhasil ditambahkan sebagai ' . $request->status;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    // ========== METHOD UNTUK EDIT MATERI & REFLEKSI ==========
    public function updateMaterial(Request $request, $id)
    {
        $request->validate([
            'material' => 'required|string',
        ]);

        $journal = TeachingJournal::findOrFail($id);
        $journal->update(['material' => $request->material]);

        return redirect()->back()->with('success', 'Materi berhasil diperbarui!');
    }

    public function updateReflection(Request $request, $id)
    {
        $request->validate([
            'reflection' => 'required|string|min:5',
        ]);

        $journal = TeachingJournal::findOrFail($id);
        $journal->update(['reflection' => $request->reflection]);

        return redirect()->back()->with('success', 'Refleksi berhasil diperbarui!');
    }

    // ========== METHOD UNTUK EDIT PRESENSI SISWA ==========
    public function updateAttendanceStatus(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:student_attendances,id',
            'status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        $attendance = StudentAttendance::findOrFail($request->attendance_id);
        $attendance->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status presensi berhasil diperbarui!',
            'new_status' => $request->status,
            'new_badge' => $this->getStatusBadge($request->status),
        ]);
    }

    private function getStatusBadge($status)
    {
        $statusMap = [
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpa' => 'danger'
        ];
        $labelMap = [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpa' => 'Alpa'
        ];
        return '<span class="badge bg-' . ($statusMap[$status] ?? 'secondary') . '">' . ($labelMap[$status] ?? $status) . '</span>';
    }

    // ========== METHOD UNTUK MATERI ==========
    public function storeMaterial(Request $request, $journalId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:file,link',
            'file' => 'required_if:type,file|file|max:10240',
            'url' => 'required_if:type,link|url|max:500',
            'description' => 'nullable|string',
            'student_option' => 'required|in:all,selected',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $journal = TeachingJournal::findOrFail($journalId);
        $firstSchedule = $journal->schedules->first();

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

        if ($request->student_option === 'selected' && $request->has('student_ids')) {
            $material->students()->attach($request->student_ids);
        }

        return redirect()->back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function destroyMaterial($journalId, $materialId)
    {
        try {
            $journal = TeachingJournal::findOrFail($journalId);
            $material = JournalMaterial::where('id', $materialId)
                ->where('teaching_journal_id', $journalId)
                ->firstOrFail();

            if ($material->type === 'file' && $material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $material->students()->detach();
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
        $firstSchedule = $journal->schedules->first();
        $students = Student::where('classroom_id', $firstSchedule->classroom_id)
            ->select('id', 'name', 'nis')
            ->orderBy('name')
            ->get();
        return response()->json($students);
    }

    public function destroy($id)
    {
        try {
            $journal = TeachingJournal::findOrFail($id);

            foreach ($journal->materials as $material) {
                if ($material->type === 'file' && $material->file_path) {
                    Storage::disk('public')->delete($material->file_path);
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
