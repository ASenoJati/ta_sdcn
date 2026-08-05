<?php

namespace App\Console\Commands;

use App\Models\TeachingSchedule;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendClassReminderNotifications extends Command
{
    protected $signature = 'notifications:send-class-reminders {--minutes=5 : Jumlah menit ke depan}';
    protected $description = 'Kirim notifikasi push untuk jadwal yang akan segera dimulai';

    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    public function handle()
    {
        $minutesAhead = (int) $this->option('minutes');
        $now = Carbon::now();
        $targetTime = $now->copy()->addMinutes($minutesAhead);

        $startTime = $targetTime->format('H:i:s');
        $startTimeLower = Carbon::parse($startTime)->subMinute()->format('H:i:s');
        $startTimeUpper = Carbon::parse($startTime)->addMinute()->format('H:i:s');

        // Log awal
        Log::info('Cron notifikasi dijalankan', [
            'time' => $now->toDateTimeString(),
            'target_time' => $targetTime->toDateTimeString(),
            'start_time_lower' => $startTimeLower,
            'start_time_upper' => $startTimeUpper,
        ]);

        $schedules = TeachingSchedule::with(['teacher', 'subject', 'classroom', 'classroom.students', 'lessonHour'])
            ->where('day', $targetTime->format('l'))
            ->whereHas('lessonHour', function ($query) use ($startTimeLower, $startTimeUpper) {
                $query->whereBetween('start_time', [$startTimeLower, $startTimeUpper]);
            })
            ->get();

        if ($schedules->isEmpty()) {
            $this->info("Tidak ada jadwal pada {$targetTime->format('H:i')}.");
            Log::info('Tidak ada jadwal ditemukan', ['target_time' => $targetTime->format('H:i')]);
            return;
        }

        foreach ($schedules as $schedule) {
            $this->sendNotificationForSchedule($schedule);
        }

        $this->info('Proses selesai.');
    }

    protected function sendNotificationForSchedule($schedule)
    {
        $userIds = collect();

        // Guru: pakai user_id (bukan teacher_id)
        if ($schedule->user_id) {
            $userIds->push($schedule->user_id);
        }

        // Siswa: jika ada user_id (untuk sistem yang punya akun siswa)
        $students = $schedule->classroom->students ?? collect();
        foreach ($students as $student) {
            if ($student->user_id) {
                $userIds->push($student->user_id);
            }
        }

        $userIds = $userIds->unique();

        // Jika tidak ada user, keluar
        if ($userIds->isEmpty()) {
            Log::warning('Tidak ada user untuk jadwal ID: ' . $schedule->id);
            $this->warn("Tidak ada user untuk jadwal ID: {$schedule->id}");
            return;
        }

        $userIds = $userIds->unique()->values();
        Log::info('User IDs ditemukan untuk jadwal ID ' . $schedule->id, ['user_ids' => $userIds->toArray()]);

        // Ambil semua token dari user_ids tersebut
        $tokens = UserFcmToken::whereIn('user_id', $userIds)->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            Log::warning('Tidak ada token untuk jadwal ID: ' . $schedule->id, [
                'user_ids' => $userIds->toArray()
            ]);
            $this->warn("Tidak ada token untuk jadwal ID: {$schedule->id}");
            return;
        }

        Log::info('Token ditemukan untuk jadwal ID ' . $schedule->id, ['tokens_count' => count($tokens)]);

        // Bangun notifikasi
        $title = "⏰ Kelas akan segera dimulai!";
        $body = sprintf(
            "%s - %s\nKelas: %s\nJam: %s - %s",
            $schedule->subject->name ?? 'Mata Pelajaran',
            $schedule->teacher->name ?? 'Guru',
            $schedule->classroom->name ?? '-',
            $schedule->lessonHour->start_time ?? '-',
            $schedule->lessonHour->end_time ?? '-'
        );

        $data = [
            'schedule_id' => $schedule->id,
            'subject' => $schedule->subject->name ?? '',
            'classroom' => $schedule->classroom->name ?? '',
            'type' => 'class_reminder',
            'timestamp' => now()->toISOString(),
        ];

        // Kirim notifikasi
        $results = $this->firebaseService->sendToMultipleDevices($tokens, $title, $body, $data);

        Log::info('Hasil pengiriman notifikasi', [
            'schedule_id' => $schedule->id,
            'total_tokens' => count($tokens),
            'success_count' => collect($results)->where('success', true)->count(),
            'failed_count' => collect($results)->where('success', false)->count(),
        ]);

        $this->info("Notifikasi dikirim untuk jadwal ID: {$schedule->id}");
    }
}
