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
        $today = now()->toDateString();
        $dayName = now()->format('l');

        $schedules = TeachingSchedule::with(['subject', 'classroom'])
            ->where('user_id', $request->user()->id)
            ->where('day', $dayName)
            ->get()
            ->map(function ($schedule) use ($today) {

                $isDone = TeachingJournal::where('teaching_schedule_id', $schedule->id)
                    ->whereDate('date', $today)
                    ->exists();

                $schedule->is_journal_filled = $isDone;

                return $schedule;
            });

        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }

    public function detail($scheduleId)
    {
        $today = now()->toDateString();

        $journal = TeachingJournal::with('attendances.student')
            ->where('teaching_schedule_id', $scheduleId)
            ->whereDate('date', $today)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $journal->id,
                'material' => $journal->material,
                'reflection' => $journal->reflection,
                'attendances' => $journal->attendances
            ]
        ]);
    }

    /**
     * 2. Ambil List Siswa berdasarkan Jadwal (Gambar 2)
     */
    public function getStudentsBySchedule($scheduleId)
    {
        // Gunakan with() untuk load classroom sekalian jika butuh data kelasnya nanti
        $schedule = TeachingSchedule::findOrFail($scheduleId);

        // Pastikan timezone sudah benar di config/app.php agar $today akurat
        $today = now('Asia/Jakarta')->toDateString();

        // 1. Ambil journal hari ini
        $journal = TeachingJournal::where('teaching_schedule_id', $scheduleId)
            ->whereDate('date', $today)
            ->first();

        // 2. Ambil semua siswa di kelas tersebut
        $students = Student::where('classroom_id', $schedule->classroom_id)
            ->orderBy('name', 'asc') // Tambahkan order agar daftar siswa rapi (A-Z)
            ->get();

        // 3. Ambil absensi jika jurnal ada
        $attendances = [];
        if ($journal) {
            $attendances = StudentAttendance::where('teaching_journal_id', $journal->id)
                ->pluck('status', 'student_id')
                ->toArray();
        }

        // 4. Map data
        $data = $students->map(function ($s) use ($attendances) {
            return [
                'id'     => $s->id,
                'name'   => $s->name,
                'nis'    => $s->nis,
                // Jika student_id tidak ada di array attendances, default ke string kosong
                'status' => $attendances[$s->id] ?? '',
            ];
        });

        return response()->json([
            'success'    => true,
            'journal_id' => $journal ? $journal->id : null,
            'material'   => $journal ? $journal->material : '',
            'data'       => $data
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
            'material' => 'required|string',
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
                'reflection' => null,
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

    public function update(Request $request, $id)
    {
        $journal = TeachingJournal::findOrFail($id);

        DB::transaction(function () use ($request, $journal) {

            $journal->update([
                'material' => $request->material
            ]);

            foreach ($request->attendances as $att) {
                StudentAttendance::updateOrCreate(
                    [
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $att['student_id']
                    ],
                    [
                        'status' => $att['status']
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Updated'
        ]);
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
