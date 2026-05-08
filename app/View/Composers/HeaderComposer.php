<?php

namespace App\View\Composers;

use App\Models\UserAttendance;
use App\Models\TeachingJournal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class HeaderComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = Auth::user();

        // Unread messages count (contoh - sesuaikan dengan sistem pesan Anda)
        $unreadMessagesCount = 0;
        $recentMessages = $this->getRecentMessages();

        // Unread notifications count
        $unreadNotificationsCount = $this->getUnreadNotificationsCount();
        $recentNotifications = $this->getRecentNotifications();

        $view->with([
            'unreadMessagesCount' => $unreadMessagesCount,
            'recentMessages' => $recentMessages,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'recentNotifications' => $recentNotifications,
        ]);
    }

    /**
     * Get recent messages (contoh data)
     */
    private function getRecentMessages()
    {
        // Ini contoh data - sesuaikan dengan sistem pesan Anda
        return [
            [
                'name' => 'Admin Sistem',
                'avatar' => asset('assets/img/default-avatar.jpg'),
                'preview' => 'Selamat datang di sistem presensi sekolah',
                'time' => '5 menit lalu',
                'starred' => true
            ],
            [
                'name' => 'Wakasek Kurikulum',
                'avatar' => asset('assets/img/default-avatar.jpg'),
                'preview' => 'Mohon segera lengkapi data jurnal pembelajaran',
                'time' => '2 jam lalu',
                'starred' => false
            ],
        ];
    }

    /**
     * Get unread notifications count
     */
    private function getUnreadNotificationsCount()
    {
        $count = 0;

        // Count presensi guru hari ini yang belum di-check-out
        $today = Carbon::today();
        $uncheckedOut = UserAttendance::whereDate('attendance_date', $today)
            ->whereNull('check_out_time')
            ->count();
        $count += $uncheckedOut;

        // Count jurnal yang belum di-review (contoh)
        $unreviewedJournals = TeachingJournal::whereDoesntHave('attendances')
            ->count();
        $count += $unreviewedJournals;

        return $count;
    }

    /**
     * Get recent notifications
     */
    private function getRecentNotifications()
    {
        $notifications = [];

        // Presensi guru hari ini
        $today = Carbon::today();
        $todayAttendances = UserAttendance::with('user')
            ->whereDate('attendance_date', $today)
            ->whereNull('check_out_time')
            ->limit(3)
            ->get();

        foreach ($todayAttendances as $attendance) {
            $notifications[] = [
                'icon' => 'bi-clock-history',
                'message' => $attendance->user->name . ' (Guru) - Belum check-out hari ini',
                'time' => 'Hari ini',
                'link' => route('admin.user-attendances.index')
            ];
        }

        // Jurnal baru
        $recentJournals = TeachingJournal::with('teachingSchedule.subject')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentJournals as $journal) {
            $notifications[] = [
                'icon' => 'bi-journal-bookmark-fill',
                'message' => 'Jurnal baru: ' . $journal->teachingSchedule->subject->name . ' (' . $journal->date->format('d/m/Y') . ')',
                'time' => $journal->created_at->diffForHumans(),
                'link' => route('admin.teaching-journals.index')
            ];
        }

        return array_slice($notifications, 0, 5);
    }
}
