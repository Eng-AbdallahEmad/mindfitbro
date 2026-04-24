<?php

namespace App\Services\Web;

use App\Models\MeetingBooking;
use App\Models\Subscription;
use App\Models\User;

class CoachDashboardService
{
    public function getData(): array
    {
        // ── المستخدمين ──────────────────────────────────────────
        $totalUsers = User::whereIn('role', ['user'])->count();

        // ── المشتركين ──────────────────────────────────────────
        $totalClients = Subscription::whereIn('status', ['active', 'waiting'])->count();

        $newClientsThisMonth = Subscription::whereIn('status', ['active', 'waiting'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── الجلسات ────────────────────────────────────────────
        $todayBookings = MeetingBooking::whereDate('meeting_date', today())
            ->where('status', 'confirmed')
            ->orderBy('meeting_time')
            ->get();

        $todaySessions    = $todayBookings->count();
        $firstSessionTime = $todayBookings->first()?->meeting_time;

        // ── الحجوزات المعلقة ───────────────────────────────────
        $pendingBookings = MeetingBooking::where('status', 'pending')->count();

        $pendingBookingsList = MeetingBooking::where('status', 'pending')
            ->with(['subscription.user', 'subscription.plan'])
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->take(5)
            ->get();

        // ── الإيرادات ──────────────────────────────────────────
        $monthlyRevenue = Subscription::whereIn('status', ['active', 'waiting'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // ── المشتركين النشطين ──────────────────────────────────
        $activeClients = Subscription::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->with(['user', 'plan'])
            ->latest('created_at')
            ->take(5)
            ->get();

        return compact(
            'totalUsers',
            'totalClients',
            'newClientsThisMonth',
            'todaySessions',
            'firstSessionTime',
            'pendingBookings',
            'pendingBookingsList',
            'monthlyRevenue',
            'activeClients',
        );
    }
}