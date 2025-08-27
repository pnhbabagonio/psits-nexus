<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import {
    Activity,
    BarChart3,
    Bell,
    Calendar,
    CreditCard,
    DollarSign,
    Eye,
    Megaphone,
    Plus,
    QrCode,
    Scan,
    TrendingUp,
    UserCheck,
    Users,
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Mock notification count
const notificationCount = 3;

// Mock recent transactions
const recentTransactions = [
    { id: 1, description: 'Membership Fee - John Doe', amount: 500.0, date: '2025-01-20', type: 'membership', method: 'QR Code' },
    { id: 2, description: 'Event Registration - Jane Smith', amount: 300.0, date: '2025-01-19', type: 'event', method: 'QR Code' },
    { id: 3, description: 'Workshop Fee - Mike Johnson', amount: 250.0, date: '2025-01-18', type: 'workshop', method: 'QR Code' },
    { id: 4, description: 'Annual Dues - Sarah Lee', amount: 800.0, date: '2025-01-17', type: 'membership', method: 'QR Code' },
    { id: 5, description: 'Seminar Fee - Alex Chen', amount: 150.0, date: '2025-01-16', type: 'seminar', method: 'QR Code' },
];

// Mock financial and engagement data
const financialSummary = {
    totalBalance: 125750.5,
    membershipFees: 45230.0,
    monthlyExpenses: 28350.75,
    totalMembers: 284,
};

// Mock QR scan analytics
const qrAnalytics = {
    totalScans: 1247,
    successfulPayments: 1180,
    failedScans: 67,
    averagePaymentAmount: 425.75,
};

// Mock engagement data
const engagementData = {
    activeMembers: 195,
    eventAttendance: 82.5,
    paymentCompliance: 94.2,
    platformUsage: 78.9,
};

// Current date
const currentDate = new Date();
const currentMonth = currentDate.toLocaleString('default', { month: 'long' });
const currentYear = currentDate.getFullYear();

// Mock calendar events for current month
const calendarEvents = [
    { date: 22, title: 'Tech Talk: AI in Business', type: 'workshop' },
    { date: 25, title: 'General Assembly Meeting', type: 'meeting' },
    { date: 28, title: 'Membership Fee Deadline', type: 'deadline' },
    { date: 30, title: 'Industry Night 2025', type: 'event' },
];

// Generate calendar days for current month
const getDaysInMonth = (date: Date) => {
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    const days = [];

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < startingDayOfWeek; i++) {
        days.push({ day: null, hasEvent: false, events: [] });
    }

    // Add all days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayEvents = calendarEvents.filter((event) => event.date === day);
        days.push({
            day,
            hasEvent: dayEvents.length > 0,
            events: dayEvents,
            isToday: day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear(),
        });
    }

    return days;
};

const calendarDays = getDaysInMonth(currentDate);

// Mock announcements
const announcements = [
    { id: 1, title: 'New Payment System Live', content: 'QR-based payment system is now fully operational', date: '2025-01-20', priority: 'high' },
    { id: 2, title: 'Membership Drive Extended', content: 'Extended deadline for new member registration', date: '2025-01-19', priority: 'medium' },
    { id: 3, title: 'System Maintenance Notice', content: 'Scheduled maintenance on Jan 25, 2025', date: '2025-01-18', priority: 'low' },
];

const quickActions = [
    {
        title: 'Generate QR Code',
        description: 'Create payment QR codes',
        icon: QrCode,
        href: '/payments/qr-generator',
        color: 'bg-blue-500 hover:bg-blue-600',
    },
    {
        title: 'Record Payment',
        description: 'Manual payment entry',
        icon: CreditCard,
        href: '/payments/record',
        color: 'bg-green-500 hover:bg-green-600',
    },
    {
        title: 'Add Member',
        description: 'Register new member',
        icon: Users,
        href: '/members/add',
        color: 'bg-purple-500 hover:bg-purple-600',
    },
    {
        title: 'Create Event',
        description: 'Setup new event',
        icon: Plus,
        href: '/events/create',
        color: 'bg-orange-500 hover:bg-orange-600',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header with Notifications and Quick Actions -->
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">PSITS Nexus Dashboard</h1>
                    <p class="text-muted-foreground">Welcome to your financial management platform</p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notifications Bell -->
                    <div class="relative">
                        <Button variant="outline" size="icon" class="relative">
                            <Bell class="h-4 w-4" />
                            <Badge
                                v-if="notificationCount > 0"
                                class="absolute -top-2 -right-2 h-5 w-5 rounded-full bg-red-500 p-0 text-xs hover:bg-red-600"
                            >
                                {{ notificationCount }}
                            </Badge>
                        </Button>
                    </div>

                    <!-- Quick Actions Dropdown -->
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button class="gap-2">
                                <Plus class="h-4 w-4" />
                                Quick Actions
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <DropdownMenuItem
                                v-for="action in quickActions"
                                :key="action.title"
                                class="cursor-pointer"
                                @click="$inertia.visit(action.href)"
                            >
                                <component :is="action.icon" class="mr-2 h-4 w-4" />
                                <div>
                                    <div class="font-medium">{{ action.title }}</div>
                                    <div class="text-xs text-muted-foreground">{{ action.description }}</div>
                                </div>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <!-- Dashboard Widgets -->
            <div class="grid gap-6">
                <!-- Key Metrics Cards -->
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-100">Total Balance</p>
                                <p class="text-2xl font-bold">₱{{ financialSummary.totalBalance.toLocaleString() }}</p>
                            </div>
                            <DollarSign class="h-8 w-8 text-blue-200" />
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-100">Membership Fees</p>
                                <p class="text-2xl font-bold">₱{{ financialSummary.membershipFees.toLocaleString() }}</p>
                            </div>
                            <UserCheck class="h-8 w-8 text-green-200" />
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-orange-100">Monthly Expenses</p>
                                <p class="text-2xl font-bold">₱{{ financialSummary.monthlyExpenses.toLocaleString() }}</p>
                            </div>
                            <CreditCard class="h-8 w-8 text-orange-200" />
                        </div>
                    </div>

                    <div class="rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-100">Total Members</p>
                                <p class="text-2xl font-bold">{{ financialSummary.totalMembers }}</p>
                            </div>
                            <Users class="h-8 w-8 text-purple-200" />
                        </div>
                    </div>
                </div>

                <!-- QR Analytics & Engagement Stats -->
                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- QR Code Analytics -->
                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">QR Payment Analytics</h3>
                            <Scan class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg bg-muted/50 p-4">
                                <div class="mb-2 flex items-center gap-2">
                                    <div class="rounded-full bg-blue-100 p-2 text-blue-600">
                                        <QrCode class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Total Scans</span>
                                </div>
                                <p class="text-2xl font-bold text-blue-600">{{ qrAnalytics.totalScans.toLocaleString() }}</p>
                            </div>
                            <div class="rounded-lg bg-muted/50 p-4">
                                <div class="mb-2 flex items-center gap-2">
                                    <div class="rounded-full bg-green-100 p-2 text-green-600">
                                        <TrendingUp class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Success Rate</span>
                                </div>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ ((qrAnalytics.successfulPayments / qrAnalytics.totalScans) * 100).toFixed(1) }}%
                                </p>
                            </div>
                            <div class="rounded-lg bg-muted/50 p-4">
                                <div class="mb-2 flex items-center gap-2">
                                    <div class="rounded-full bg-yellow-100 p-2 text-yellow-600">
                                        <DollarSign class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Avg. Payment</span>
                                </div>
                                <p class="text-2xl font-bold text-yellow-600">₱{{ qrAnalytics.averagePaymentAmount.toFixed(2) }}</p>
                            </div>
                            <div class="rounded-lg bg-muted/50 p-4">
                                <div class="mb-2 flex items-center gap-2">
                                    <div class="rounded-full bg-red-100 p-2 text-red-600">
                                        <Activity class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Failed Scans</span>
                                </div>
                                <p class="text-2xl font-bold text-red-600">{{ qrAnalytics.failedScans }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Engagement Analytics -->
                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Engagement Analytics</h3>
                            <BarChart3 class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Active Members</span>
                                    <span class="font-medium">{{ engagementData.activeMembers }}/{{ financialSummary.totalMembers }}</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-muted">
                                    <div
                                        class="h-2 rounded-full bg-blue-500"
                                        :style="{ width: (engagementData.activeMembers / financialSummary.totalMembers) * 100 + '%' }"
                                    ></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Event Attendance</span>
                                    <span class="font-medium">{{ engagementData.eventAttendance }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-muted">
                                    <div class="h-2 rounded-full bg-green-500" :style="{ width: engagementData.eventAttendance + '%' }"></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Payment Compliance</span>
                                    <span class="font-medium">{{ engagementData.paymentCompliance }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-muted">
                                    <div class="h-2 rounded-full bg-purple-500" :style="{ width: engagementData.paymentCompliance + '%' }"></div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Platform Usage</span>
                                    <span class="font-medium">{{ engagementData.platformUsage }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-muted">
                                    <div class="h-2 rounded-full bg-orange-500" :style="{ width: engagementData.platformUsage + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Recent QR Transactions -->
                    <div class="rounded-xl border border-border bg-card p-6 lg:col-span-2">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Recent QR Transactions</h3>
                            <Button variant="outline" size="sm">
                                <Eye class="mr-2 h-4 w-4" />
                                View All
                            </Button>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="transaction in recentTransactions"
                                :key="transaction.id"
                                class="flex items-center justify-between rounded-lg bg-muted/50 p-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="rounded-full bg-green-100 p-2 text-green-600">
                                        <QrCode class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ transaction.description }}</p>
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                            <span>{{ transaction.date }}</span>
                                            <Badge variant="outline" class="text-xs">{{ transaction.type }}</Badge>
                                            <Badge variant="secondary" class="text-xs">{{ transaction.method }}</Badge>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-semibold text-green-600">+₱{{ transaction.amount.toLocaleString() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Calendar -->
                    <div class="rounded-xl border border-border bg-card p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">{{ currentMonth }} {{ currentYear }}</h3>
                            <Calendar class="h-5 w-5 text-muted-foreground" />
                        </div>

                        <!-- Calendar Header -->
                        <div class="mb-2 grid grid-cols-7 gap-1">
                            <div
                                v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                                :key="day"
                                class="p-2 text-center text-xs font-medium text-muted-foreground"
                            >
                                {{ day }}
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-1">
                            <div
                                v-for="(dayData, index) in calendarDays"
                                :key="index"
                                :class="[
                                    'relative cursor-pointer rounded p-2 text-center text-sm transition-colors',
                                    dayData.day ? 'hover:bg-muted' : '',
                                    dayData.isToday ? 'bg-primary font-bold text-primary-foreground' : '',
                                    dayData.hasEvent ? 'border border-blue-200 bg-blue-50' : '',
                                ]"
                            >
                                <span v-if="dayData.day">{{ dayData.day }}</span>
                                <div
                                    v-if="dayData.hasEvent"
                                    class="absolute bottom-0 left-1/2 h-1 w-1 -translate-x-1/2 transform rounded-full bg-blue-500"
                                ></div>
                            </div>
                        </div>

                        <!-- Event List -->
                        <div class="mt-4 space-y-2">
                            <h4 class="mb-2 text-sm font-medium">Upcoming Events</h4>
                            <div v-for="event in calendarEvents" :key="event.date" class="flex items-center gap-2 rounded bg-muted/50 p-2 text-xs">
                                <div
                                    :class="[
                                        'h-2 w-2 rounded-full',
                                        event.type === 'workshop' ? 'bg-blue-500' : '',
                                        event.type === 'meeting' ? 'bg-green-500' : '',
                                        event.type === 'deadline' ? 'bg-red-500' : '',
                                        event.type === 'event' ? 'bg-purple-500' : '',
                                    ]"
                                ></div>
                                <span class="font-medium">{{ event.date }}</span>
                                <span>{{ event.title }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements Feed -->
                <div class="rounded-xl border border-border bg-card p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">System Announcements</h3>
                        <Megaphone class="h-5 w-5 text-muted-foreground" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div
                            v-for="announcement in announcements"
                            :key="announcement.id"
                            class="cursor-pointer rounded-lg bg-muted/50 p-4 transition-colors hover:bg-muted/70"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <h4 class="text-sm font-medium">{{ announcement.title }}</h4>
                                <Badge
                                    :class="{
                                        'bg-red-100 text-red-700': announcement.priority === 'high',
                                        'bg-yellow-100 text-yellow-700': announcement.priority === 'medium',
                                        'bg-gray-100 text-gray-700': announcement.priority === 'low',
                                    }"
                                >
                                    {{ announcement.priority }}
                                </Badge>
                            </div>
                            <p class="mb-2 text-xs text-muted-foreground">{{ announcement.content }}</p>
                            <p class="text-xs text-muted-foreground">{{ announcement.date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
