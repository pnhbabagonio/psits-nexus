<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real user/member data
        $totalMembers = User::count();
        $activeMembers = User::where('account_status', 'active')->count();
        
        // Mock data for now - you can replace these with real models later
        $financialSummary = [
            'totalBalance' => 125750.5, // Replace with real financial data
            'membershipFees' => 45230.0, // Sum from payments table
            'monthlyExpenses' => 28350.75, // Sum from expenses table
            'totalMembers' => $totalMembers,
        ];

        // Recent transactions - replace with real transaction model
        $recentTransactions = [
            ['id' => 1, 'description' => 'Membership Fee - John Doe', 'amount' => 500.0, 'date' => '2025-01-20', 'type' => 'membership', 'method' => 'QR Code'],
            ['id' => 2, 'description' => 'Event Registration - Jane Smith', 'amount' => 300.0, 'date' => '2025-01-19', 'type' => 'event', 'method' => 'QR Code'],
            ['id' => 3, 'description' => 'Workshop Fee - Mike Johnson', 'amount' => 250.0, 'date' => '2025-01-18', 'type' => 'workshop', 'method' => 'QR Code'],
            ['id' => 4, 'description' => 'Annual Dues - Sarah Lee', 'amount' => 800.0, 'date' => '2025-01-17', 'type' => 'membership', 'method' => 'QR Code'],
            ['id' => 5, 'description' => 'Seminar Fee - Alex Chen', 'amount' => 150.0, 'date' => '2025-01-16', 'type' => 'seminar', 'method' => 'QR Code'],
        ];

        // QR Analytics - replace with real data
        $qrAnalytics = [
            'totalScans' => 1247,
            'successfulPayments' => 1180,
            'failedScans' => 67,
            'averagePaymentAmount' => 425.75,
        ];

        // Real engagement data
        $engagementData = [
            'activeMembers' => $activeMembers,
            'eventAttendance' => 82.5, // Calculate from events/attendees
            'paymentCompliance' => 94.2, // Calculate from payments
            'platformUsage' => $this->calculatePlatformUsage(),
        ];

        // Calendar events - replace with real events
        $calendarEvents = [
            ['date' => 22, 'title' => 'Tech Talk: AI in Business', 'type' => 'workshop'],
            ['date' => 25, 'title' => 'General Assembly Meeting', 'type' => 'meeting'],
            ['date' => 28, 'title' => 'Membership Fee Deadline', 'type' => 'deadline'],
            ['date' => 30, 'title' => 'Industry Night 2025', 'type' => 'event'],
        ];

        // Announcements
        $announcements = [
            ['id' => 1, 'title' => 'New Payment System Live', 'content' => 'QR-based payment system is now fully operational', 'date' => '2025-01-20', 'priority' => 'high'],
            ['id' => 2, 'title' => 'Membership Drive Extended', 'content' => 'Extended deadline for new member registration', 'date' => '2025-01-19', 'priority' => 'medium'],
            ['id' => 3, 'title' => 'System Maintenance Notice', 'content' => 'Scheduled maintenance on Jan 25, 2025', 'date' => '2025-01-18', 'priority' => 'low'],
        ];

        return Inertia::render('Dashboard', [
            'financialSummary' => $financialSummary,
            'recentTransactions' => $recentTransactions,
            'qrAnalytics' => $qrAnalytics,
            'engagementData' => $engagementData,
            'calendarEvents' => $calendarEvents,
            'announcements' => $announcements,
            'notificationCount' => 3, // Calculate real notifications
        ]);
    }

    /**
     * Calculate platform usage percentage based on recent logins
     */
    private function calculatePlatformUsage()
    {
        $totalUsers = User::count();
        if ($totalUsers === 0) return 0;

        $recentlyActiveUsers = User::where('last_login', '>=', Carbon::now()->subDays(30))->count();
        
        return round(($recentlyActiveUsers / $totalUsers) * 100, 1);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        return response()->json([
            'total_members' => User::count(),
            'active_members' => User::where('account_status', 'active')->count(),
            'student_members' => User::where('role_id', 3)->count(), // Assuming role_id 3 is student
            'admin_members' => User::where('role_id', 1)->count(), // Assuming role_id 1 is admin
            'recent_registrations' => User::where('date_registered', '>=', Carbon::now()->subDays(7))->count(),
        ]);
    }
}