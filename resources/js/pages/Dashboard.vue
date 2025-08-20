<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Bell, Plus, QrCode, CreditCard, Users, FileText, Download } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Mock notification count
const notificationCount = 3;

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
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
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

            <!-- Quick Action Cards (Alternative Layout) -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4 mb-4">
                <button
                    v-for="action in quickActions"
                    :key="action.title"
                    @click="$inertia.visit(action.href)"
                    :class="[
                        'relative p-4 rounded-xl text-white transition-all duration-200 hover:scale-105 hover:shadow-lg group',
                        action.color
                    ]"
                >
                    <div class="flex items-center gap-3">
                        <component :is="action.icon" class="h-6 w-6 flex-shrink-0" />
                        <div class="text-left">
                            <div class="font-semibold text-sm">{{ action.title }}</div>
                            <div class="text-xs opacity-90">{{ action.description }}</div>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Original Dashboard Content -->
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