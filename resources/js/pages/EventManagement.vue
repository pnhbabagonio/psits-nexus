<!-- Event Management.vue -->
<script setup lang="ts">
import { ref } from "vue"
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import EventsList from "@/components/events/EventsList.vue"
import EventCalendar from "@/components/events/EventCalendar.vue"
import AttendeeManagement from "@/components/events/AttendeeManagement.vue"
import EventAnalytics from "@/components/events/EventAnalytics.vue"
import { Calendar, Users, BarChart3, ListChecks } from 'lucide-vue-next'

const activeTab = ref("events")

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Event Management', href: '/event-management' },
]

const tabs = [
    { key: "events", label: "Events", icon: ListChecks },
    { key: "calendar", label: "Calendar", icon: Calendar },
    { key: "attendees", label: "Attendees", icon: Users },
    { key: "analytics", label: "Analytics", icon: BarChart3 },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <!-- Header Section -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-foreground mb-2">Event Management</h1>
                <p class="text-muted-foreground">Manage your events, attendees, and track analytics</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex space-x-1 border-b border-border mb-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    class="flex items-center gap-2 pb-3 px-4 font-medium transition-all relative"
                    :class="[
                        activeTab === tab.key
                            ? 'text-primary'
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                >
                    <component :is="tab.icon" class="w-4 h-4" />
                    <span>{{ tab.label }}</span>
                    <div
                        v-if="activeTab === tab.key"
                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"
                    />
                </button>
            </div>

            <!-- Tab Content -->
            <div class="animate-in fade-in-50 duration-200">
                <EventsList v-if="activeTab === 'events'" />
                <EventCalendar v-if="activeTab === 'calendar'" />
                <AttendeeManagement v-if="activeTab === 'attendees'" />
                <EventAnalytics v-if="activeTab === 'analytics'" />
            </div>
        </div>
    </AppLayout>
</template>