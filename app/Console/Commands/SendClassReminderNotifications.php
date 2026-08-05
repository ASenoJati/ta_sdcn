<?php

namespace App\Console\Commands;

use App\Models\TeachingSchedule;
use App\Models\UserFcmToken;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendClassReminderNotifications extends Command
{
    protected $signature = 'notifications:send-class-reminders {--minutes=5 : Jumlah menit ke depan untuk mencari jadwal yang akan dimulai}';

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
        // Beri toleransi 1 menit agar lebih fleksibel
        $startTimeLower = Carbon::parse($startTime)->subMinute()->format('H:i:s');
        $startTimeUpper = Carbon::parse($startTime)->addMinute()->format('H:i:s');

        $schedules = TeachingSchedule::with(['teacher', 'subject', 'classroom', 'lessonHour'])
            ->where('day', $targetTime->format('l'))
            ->whereHas('lessonHour', function ($query) use ($startTimeLower, $startTimeUpper) {
                $query->whereBetween('start_time', [$startTimeLower, $startTimeUpper]);
            })
            ->get();

        if ($schedules->isEmpty()) {
            $this->info("Tidak ada jadwal pada {$targetTime->format('H:i')} (plus/minus 1 menit).");
            return;
        }

        foreach ($schedules as $schedule) {
            $this->sendNotificationForSchedule($schedule);
        }

        $this->info('Notifikasi terkirim untuk ' . $schedules->count() . ' jadwal.');
    }

    protected function sendNotificationForSchedule($schedule)
    {
        // Kumpulkan user_id (guru + siswa)
        $userIds = collect();
        if ($schedule->teacher_id) {
            $userIds->push($schedule->teacher_id);
        }
        $students = $schedule->classroom->students ?? collect();
        foreach ($students as $student) {
            if ($student->user_id) {
                $userIds->push($student->user_id);
            }
        }

        $userIds = $userIds->unique();
        $tokens = UserFcmToken::whereIn('user_id', $userIds)->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            $this->warn("Tidak ada token untuk jadwal ID: {$schedule->id}");
            return;
        }

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

        $this->firebaseService->sendToMultipleDevices($tokens, $title, $body, $data);
        Log::info('Notifikasi dikirim', ['schedule_id' => $schedule->id, 'tokens_count' => count($tokens)]);
    }
}
