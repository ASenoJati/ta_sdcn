<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\TeachingJournal;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    /**
     * 1. Tampilkan Jadwal Mengajar Hari Ini (Gambar 1)
     */
    public function index(Request $request)
    {
        $dayName = now()->format('l'); // Mendapatkan hari ini (English)
        $schedules = TeachingSchedule::with(['subject', 'classroom'])
            ->where('user_id', $request->user()->id)
            ->where('day', $dayName)
            ->get();

        return response()->json(['success' => true, 'data' => $schedules]);
    }

    /**
     * 2. Ambil List Siswa berdasarkan Jadwal (Gambar 2)
     */
    public function getStudentsBySchedule($scheduleId)
    {
        $schedule = TeachingSchedule::findOrFail($scheduleId);
        $students = Student::where('classroom_id', $schedule->classroom_id)->get();

        return response()->json([
            'success' => true,
            'classroom' => $schedule->classroom->name,
            'data' => $students
        ]);
    }

    /**
     * TAHAP 1: Simpan Presensi Siswa
     * Setelah klik "Simpan Presensi" di Gambar 2
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'teaching_schedule_id' => 'required|exists:teaching_schedules,id',
            'material' => 'required|string', // Materi pelajaran (Teks Eksplanasi)
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpa',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Buat Header Jurnal (Tanpa Refleksi Dulu)
            $journal = TeachingJournal::create([
                'teaching_schedule_id' => $request->teaching_schedule_id,
                'date' => now(),
                'material' => $request->material,
                'reflection' => null, // Masih kosong
            ]);

            // 2. Simpan Detail Absensi
            foreach ($request->attendances as $att) {
                StudentAttendance::create([
                    'teaching_journal_id' => $journal->id,
                    'student_id' => $att['student_id'],
                    'status' => $att['status'],
                ]);
            }

            // Kembalikan ID Jurnal agar Flutter bisa menggunakannya untuk simpan refleksi
            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil disimpan. Silahkan isi refleksi.',
                'journal_id' => $journal->id
            ]);
        });
    }

    /**
     * TAHAP 2: Simpan Refleksi (Endpoint Sendiri)
     * Setelah klik "Simpan Refleksi" di Gambar 3
     */
    public function storeReflection(Request $request, $journalId)
    {
        $request->validate([
            'reflection' => 'required|string|min:5',
        ]);

        $journal = TeachingJournal::findOrFail($journalId);

        $journal->update([
            'reflection' => $request->reflection
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan refleksi berhasil disimpan',
            'data' => $journal
        ]);
    }
}
