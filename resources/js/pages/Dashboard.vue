<<<<<<< HEAD
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
=======
// Mock recent transactions
const recentTransactions = [
    { id: 1, description: 'Membership Fee - John Doe', amount: 500.00, date: '2025-01-20', type: 'membership', method: 'QR Code' },
    { id: 2, description: 'Event Registration - Jane Smith', amount: 300.00, date: '2025-01-19', type: 'event', method: 'QR Code' },
    { id: 3, description: 'Workshop Fee - Mike Johnson', amount: 250.00, date: '2025-01-18', type: 'workshop', method: 'QR Code' },
    { id: 4, description: 'Annual Dues - Sarah Lee', amount: 800.00, date: '2025-01-17', type: 'membership', method: 'QR Code' },
    { id: 5, description: 'Seminar Fee - Alex Chen', amount: 150.00, date: '2025-01-16', type: 'seminar', method: 'QR Code' }
];<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Bell, Plus, QrCode, CreditCard, Users, FileText, Download, DollarSign, TrendingUp, Calendar, Megaphone, Eye, BarChart3, Activity, Scan, UserCheck } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
>>>>>>> f0faa7218f40f1b5eaaae6e30ca122c99aa743ea
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
<<<<<<< HEAD
=======

// Mock notification count
const notificationCount = 3;

// Mock recent transactions
const recentTransactions = [
    { id: 1, description: 'Membership Fee - John Doe', amount: 500.00, date: '2025-01-20', type: 'membership', method: 'QR Code' },
    { id: 2, description: 'Event Registration - Jane Smith', amount: 300.00, date: '2025-01-19', type: 'event', method: 'QR Code' },
    { id: 3, description: 'Workshop Fee - Mike Johnson', amount: 250.00, date: '2025-01-18', type: 'workshop', method: 'QR Code' },
    { id: 4, description: 'Annual Dues - Sarah Lee', amount: 800.00, date: '2025-01-17', type: 'membership', method: 'QR Code' },
    { id: 5, description: 'Seminar Fee - Alex Chen', amount: 150.00, date: '2025-01-16', type: 'seminar', method: 'QR Code' }
];

// Mock financial and engagement data
const financialSummary = {
    totalBalance: 125750.50,
    membershipFees: 45230.00,
    monthlyExpenses: 28350.75,
    totalMembers: 284
};

// Mock QR scan analytics
const qrAnalytics = {
    totalScans: 1247,
    successfulPayments: 1180,
    failedScans: 67,
    averagePaymentAmount: 425.75
};

// Mock engagement data
const engagementData = {
    activeMembers: 195,
    eventAttendance: 82.5,
    paymentCompliance: 94.2,
    platformUsage: 78.9
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
    { date: 30, title: 'Industry Night 2025', type: 'event' }
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
        const dayEvents = calendarEvents.filter(event => event.date === day);
        days.push({ 
            day, 
            hasEvent: dayEvents.length > 0, 
            events: dayEvents,
            isToday: day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()
        });
    }
    
    return days;
};

const calendarDays = getDaysInMonth(currentDate);

// Mock announcements
const announcements = [
    { id: 1, title: 'New Payment System Live', content: 'QR-based payment system is now fully operational', date: '2025-01-20', priority: 'high' },
    { id: 2, title: 'Membership Drive Extended', content: 'Extended deadline for new member registration', date: '2025-01-19', priority: 'medium' },
    { id: 3, title: 'System Maintenance Notice', content: 'Scheduled maintenance on Jan 25, 2025', date: '2025-01-18', priority: 'low' }
];

const quickActions = [
    {
        title: 'Generate QR Code',
        description: 'Create payment QR codes',
        icon: QrCode,
        href: '/payments/qr-generator',
        color: 'bg-blue-500 hover:bg-blue-600'
    },
    {
        title: 'Record Payment',
        description: 'Manual payment entry',
        icon: CreditCard,
        href: '/payments/record',
        color: 'bg-green-500 hover:bg-green-600'
    },
    {
        title: 'Add Member',
        description: 'Register new member',
        icon: Users,
        href: '/members/add',
        color: 'bg-purple-500 hover:bg-purple-600'
    },
    {
        title: 'Create Event',
        description: 'Setup new event',
        icon: Plus,
        href: '/events/create',
        color: 'bg-orange-500 hover:bg-orange-600'
    },
];
>>>>>>> f0faa7218f40f1b5eaaae6e30ca122c99aa743ea
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
<<<<<<< HEAD
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
=======
            <!-- Header with Notifications and Quick Actions -->
            <div class="flex items-center justify-between mb-4">
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
                                class="absolute -top-2 -right-2 h-5 w-5 rounded-full p-0 text-xs bg-red-500 hover:bg-red-600"
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
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">Total Balance</p>
                                <p class="text-2xl font-bold">₱{{ financialSummary.totalBalance.toLocaleString() }}</p>
                            </div>
                            <DollarSign class="h-8 w-8 text-blue-200" />
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Membership Fees</p>
                                <p class="text-2xl font-bold">₱{{ financialSummary.membershipFees.toLocaleString() }}</p>
                            </div>
                            <UserCheck class="h-8 w-8 text-green-200" />
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">Monthly Expenses</p>
                                <p class="text-2xl font-bold">₱{{ financialSummary.monthlyExpenses.toLocaleString() }}</p>
                            </div>
                            <CreditCard class="h-8 w-8 text-orange-200" />
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-xl text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm">Total Members</p>
                                <p class="text-2xl font-bold">{{ financialSummary.totalMembers }}</p>
                            </div>
                            <Users class="h-8 w-8 text-purple-200" />
                        </div>
                    </div>
                </div>

                <!-- QR Analytics & Engagement Stats -->
                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- QR Code Analytics -->
                    <div class="bg-card border border-border rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">QR Payment Analytics</h3>
                            <Scan class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-2 bg-blue-100 text-blue-600 rounded-full">
                                        <QrCode class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Total Scans</span>
                                </div>
                                <p class="text-2xl font-bold text-blue-600">{{ qrAnalytics.totalScans.toLocaleString() }}</p>
                            </div>
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-2 bg-green-100 text-green-600 rounded-full">
                                        <TrendingUp class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Success Rate</span>
                                </div>
                                <p class="text-2xl font-bold text-green-600">{{ ((qrAnalytics.successfulPayments / qrAnalytics.totalScans) * 100).toFixed(1) }}%</p>
                            </div>
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-full">
                                        <DollarSign class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Avg. Payment</span>
                                </div>
                                <p class="text-2xl font-bold text-yellow-600">₱{{ qrAnalytics.averagePaymentAmount.toFixed(2) }}</p>
                            </div>
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-2 bg-red-100 text-red-600 rounded-full">
                                        <Activity class="h-4 w-4" />
                                    </div>
                                    <span class="text-sm font-medium">Failed Scans</span>
                                </div>
                                <p class="text-2xl font-bold text-red-600">{{ qrAnalytics.failedScans }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Engagement Analytics -->
                    <div class="bg-card border border-border rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Engagement Analytics</h3>
                            <BarChart3 class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Active Members</span>
                                    <span class="font-medium">{{ engagementData.activeMembers }}/{{ financialSummary.totalMembers }}</span>
                                </div>
                                <div class="w-full bg-muted rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" :style="{ width: (engagementData.activeMembers / financialSummary.totalMembers * 100) + '%' }"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Event Attendance</span>
                                    <span class="font-medium">{{ engagementData.eventAttendance }}%</span>
                                </div>
                                <div class="w-full bg-muted rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" :style="{ width: engagementData.eventAttendance + '%' }"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Payment Compliance</span>
                                    <span class="font-medium">{{ engagementData.paymentCompliance }}%</span>
                                </div>
                                <div class="w-full bg-muted rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" :style="{ width: engagementData.paymentCompliance + '%' }"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Platform Usage</span>
                                    <span class="font-medium">{{ engagementData.platformUsage }}%</span>
                                </div>
                                <div class="w-full bg-muted rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full" :style="{ width: engagementData.platformUsage + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Recent QR Transactions -->
                    <div class="lg:col-span-2 bg-card border border-border rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Recent QR Transactions</h3>
                            <Button variant="outline" size="sm">
                                <Eye class="h-4 w-4 mr-2" />
                                View All
                            </Button>
                        </div>
                        <div class="space-y-3">
                            <div 
                                v-for="transaction in recentTransactions" 
                                :key="transaction.id"
                                class="flex items-center justify-between p-3 rounded-lg bg-muted/50"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-green-100 text-green-600">
                                        <QrCode class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm">{{ transaction.description }}</p>
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                            <span>{{ transaction.date }}</span>
                                            <Badge variant="outline" class="text-xs">{{ transaction.type }}</Badge>
                                            <Badge variant="secondary" class="text-xs">{{ transaction.method }}</Badge>
                                        </div>
                                    </div>
                                </div>
                                <div class="font-semibold text-green-600">
                                    +₱{{ transaction.amount.toLocaleString() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Calendar -->
                    <div class="bg-card border border-border rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">{{ currentMonth }} {{ currentYear }}</h3>
                            <Calendar class="h-5 w-5 text-muted-foreground" />
                        </div>
                        
                        <!-- Calendar Header -->
                        <div class="grid grid-cols-7 gap-1 mb-2">
                            <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" 
                                 :key="day" 
                                 class="text-center text-xs font-medium text-muted-foreground p-2">
                                {{ day }}
                            </div>
                        </div>
                        
                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-1">
                            <div v-for="(dayData, index) in calendarDays" 
                                 :key="index"
                                 :class="[
                                     'relative p-2 text-center text-sm rounded cursor-pointer transition-colors',
                                     dayData.day ? 'hover:bg-muted' : '',
                                     dayData.isToday ? 'bg-primary text-primary-foreground font-bold' : '',
                                     dayData.hasEvent ? 'bg-blue-50 border border-blue-200' : ''
                                 ]">
                                <span v-if="dayData.day">{{ dayData.day }}</span>
                                <div v-if="dayData.hasEvent" 
                                     class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                            </div>
                        </div>
                        
                        <!-- Event List -->
                        <div class="mt-4 space-y-2">
                            <h4 class="font-medium text-sm mb-2">Upcoming Events</h4>
                            <div v-for="event in calendarEvents" 
                                 :key="event.date"
                                 class="flex items-center gap-2 p-2 rounded bg-muted/50 text-xs">
                                <div :class="[
                                    'w-2 h-2 rounded-full',
                                    event.type === 'workshop' ? 'bg-blue-500' : '',
                                    event.type === 'meeting' ? 'bg-green-500' : '',
                                    event.type === 'deadline' ? 'bg-red-500' : '',
                                    event.type === 'event' ? 'bg-purple-500' : ''
                                ]"></div>
                                <span class="font-medium">{{ event.date }}</span>
                                <span>{{ event.title }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements Feed -->
                <div class="bg-card border border-border rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">System Announcements</h3>
                        <Megaphone class="h-5 w-5 text-muted-foreground" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div 
                            v-for="announcement in announcements" 
                            :key="announcement.id"
                            class="p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-colors cursor-pointer"
                        >
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-medium text-sm">{{ announcement.title }}</h4>
                                <Badge :class="{
                                    'bg-red-100 text-red-700': announcement.priority === 'high',
                                    'bg-yellow-100 text-yellow-700': announcement.priority === 'medium',
                                    'bg-gray-100 text-gray-700': announcement.priority === 'low'
                                }">
                                    {{ announcement.priority }}
                                </Badge>
                            </div>
                            <p class="text-xs text-muted-foreground mb-2">{{ announcement.content }}</p>
                            <p class="text-xs text-muted-foreground">{{ announcement.date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
>>>>>>> f0faa7218f40f1b5eaaae6e30ca122c99aa743ea
