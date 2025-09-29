<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import { CalendarDays, Users2, CalendarCheck2, Award, Plus, Filter, ChevronDown } from 'lucide-vue-next'
import EventForm from '@/components/EventForm.vue'
import EventDetail from '@/components/EventDetail.vue'
import { ref, computed, onMounted, watch } from 'vue'

const isEditing = ref(false)
const isLoading = ref(false)

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Events', href: '/events' },
]

// Reactive data
const stats = ref({
    total_events: 0,
    active_registrations: 0,
    upcoming_events: 0,
    average_attendance: 0
})

const recentEvents = ref<AppEvent[]>([])
const recentActivity = ref<Activity[]>([])

interface AppEvent {
    id: number
    title: string
    description: string
    date: string
    time: string
    venue: string
    address: string
    status: 'Upcoming' | 'Ongoing' | 'Completed'
    registered: string
    max_capacity: number
    organizer: string
    created_at: string
    updated_at: string
}

interface Activity {
    text: string
    time: string
    color: string
}

// Load events from API
async function loadEvents(statusFilter = 'All') {
    isLoading.value = true
    try {
        const response = await fetch(`/events/data?status=${statusFilter}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })

        if (!response.ok) {
            throw new Error('Failed to fetch events')
        }

        const data = await response.json()

        recentEvents.value = data.events
        stats.value = {
            total_events: data.stats.total_events,
            active_registrations: data.stats.active_registrations,
            upcoming_events: data.stats.upcoming_events,
            average_attendance: Math.round((data.stats.average_attendance || 0) * 10) / 10
        }

        // Generate recent activity from events
        updateRecentActivity()
    } catch (error) {
        console.error('Failed to load events:', error)
        // You might want to show a user-friendly error message here
    } finally {
        isLoading.value = false
    }
}

function updateRecentActivity() {
    recentActivity.value = recentEvents.value.slice(0, 3).map(event => ({
        text: `Event: ${event.title}`,
        time: formatTimeAgo(new Date(event.created_at)),
        color: getActivityColor(event.status)
    }))
}

function getActivityColor(status: string): string {
    const colors = {
        'Upcoming': 'text-blue-600',
        'Ongoing': 'text-emerald-600',
        'Completed': 'text-gray-600'
    }
    return colors[status as keyof typeof colors] || 'text-gray-600'
}

function formatTimeAgo(date: Date): string {
    const now = new Date()
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000)

    if (diffInSeconds < 60) return 'Just now'
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
    return `${Math.floor(diffInSeconds / 86400)} days ago`
}

function openCreateForm() {
    isEditing.value = false
    selectedEvent.value = null
    showForm.value = true
}

// Form and modal state
const showForm = ref(false)
const selectedEvent = ref<AppEvent | null>(null)
const showEventDetail = ref(false)

// Event Filter
const eventFilter = ref('All')
const isEventFilterOpen = ref(false)

const eventFilterOptions = [
    'All',
    'Upcoming',
    'Ongoing',
    'Completed'
]

// Filtered events based on status
const filteredRecentEvents = computed(() => {
    if (eventFilter.value === 'All') {
        return recentEvents.value
    }
    return recentEvents.value.filter(event => event.status === eventFilter.value)
})

// Function to open event detail
function openEventDetail(event: AppEvent) {
    selectedEvent.value = event
    showEventDetail.value = true
}

// Function to close event detail
function closeEventDetail() {
    selectedEvent.value = null
    showEventDetail.value = false
}

// Function to handle new event creation with optimistic update
async function handleEventCreated(newEvent: AppEvent) {
    console.log('🎯 handleEventCreated called with:', newEvent)
    showForm.value = false

    // Check if it's a temporary event (negative ID)
    const isTemporaryEvent = newEvent.id < 0

    // Store current state for rollback
    const previousEvents = [...recentEvents.value]
    const previousStats = { ...stats.value }

    // Optimistically add the event to UI (at the top)
    recentEvents.value = [newEvent, ...recentEvents.value]

    // Optimistically update stats
    stats.value = {
        ...stats.value,
        total_events: stats.value.total_events + 1,
        upcoming_events: newEvent.status === 'Upcoming' ? stats.value.upcoming_events + 1 : stats.value.upcoming_events
    }

    // Add to recent activity optimistically
    recentActivity.value.unshift({
        text: `New event created: ${newEvent.title}`,
        time: 'Just now',
        color: 'text-emerald-600'
    })

    // Keep only the latest 3 activities
    if (recentActivity.value.length > 3) {
        recentActivity.value = recentActivity.value.slice(0, 3)
    }

    console.log('⚡ UI updated optimistically')

    // Only sync with server if it's a temporary event
    if (isTemporaryEvent) {
        try {
            await loadEvents(eventFilter.value)
            console.log('✅ Server sync completed - temporary event replaced with real one')
        } catch (error) {
            console.error('❌ Server sync failed, rolling back:', error)

            // Rollback on error
            recentEvents.value = previousEvents
            stats.value = previousStats

            // Remove the recent activity we added
            recentActivity.value = recentActivity.value.filter(activity =>
                !activity.text.includes(`New event created: ${newEvent.title}`)
            )

            alert('Failed to sync event with server. Please refresh the page.')
        }
    }
}

// Open EventForm for editing
function handleEditEvent(event: AppEvent) {
    selectedEvent.value = event
    isEditing.value = true
    showForm.value = true
    showEventDetail.value = false
}

// When form is saved, update existing event
async function handleEventUpdated(updatedEvent: AppEvent) {
    console.log('🔄 handleEventUpdated called with:', updatedEvent)
    showForm.value = false
    isEditing.value = false
    selectedEvent.value = null

    await loadEvents(eventFilter.value)
    console.log('✅ Events reloaded after update')
}


// Delete event using Inertia with optimistic update
function handleEventDeleted(eventId: number) {
    console.log('🗑️ Deleting event:', eventId)

    // Store current state for rollback
    const previousEvents = [...recentEvents.value]
    const previousStats = { ...stats.value }

    // Optimistically remove the event from UI
    recentEvents.value = recentEvents.value.filter(event => event.id !== eventId)

    // Optimistically update stats
    const deletedEvent = previousEvents.find(e => e.id === eventId)
    if (deletedEvent) {
        stats.value = {
            ...stats.value,
            total_events: stats.value.total_events - 1,
            active_registrations: stats.value.active_registrations - (parseInt(deletedEvent.registered) || 0),
            upcoming_events: deletedEvent.status === 'Upcoming' ? stats.value.upcoming_events - 1 : stats.value.upcoming_events
        }
    }

    // Close detail modal immediately
    showEventDetail.value = false

    // Then make the actual delete request
    router.delete(`/events/${eventId}`, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('✅ Delete successful via Inertia')
            // Optional: Reload to ensure sync, but UI is already updated
            loadEvents(eventFilter.value)
        },
        onError: (errors) => {
            console.error('❌ Delete failed:', errors)
            // Rollback on error
            recentEvents.value = previousEvents
            stats.value = previousStats
            alert('Failed to delete event. Please try again.')
        }
    })
}
// Watch for filter changes
watch(eventFilter, (newFilter) => {
    loadEvents(newFilter)
})

// Load events on component mount
onMounted(() => {
    loadEvents()
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Event Management" />

        <div class="min-h-screen bg-background p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Event Management</h1>
                    <p class="text-muted-foreground">Manage and monitor PSITS events, registrations, and analytics</p>
                </div>
                <button @click="openCreateForm"
                    class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                    <Plus class="h-4 w-4" />
                    Create Event
                </button>
            </div>

            <!-- Stats cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">Total Events</p>
                        <CalendarCheck2 class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.total_events }}</div>
                </div>

                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">Active Registrations</p>
                        <Users2 class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.active_registrations }}</div>
                </div>

                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">Upcoming Events</p>
                        <CalendarDays class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.upcoming_events }}</div>
                </div>

                <div class="rounded-lg border bg-card p-6 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">Average Attendance</p>
                        <Award class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="text-2xl font-bold">{{ stats.average_attendance }}%</div>
                </div>
            </div>

            <!-- Event Form -->
            <div v-if="showForm" class="mb-6">
                <EventForm :isOpen="showForm" :event="isEditing ? selectedEvent : null" :isEditing="isEditing"
                    @close="() => { showForm = false; isEditing = false; selectedEvent = null }"
                    @created="handleEventCreated" @updated="handleEventUpdated" />
            </div>

            <!-- Event Detail Modal -->
            <EventDetail v-if="selectedEvent" :event="selectedEvent" :isOpen="showEventDetail" @close="closeEventDetail"
                @edit="handleEditEvent" @delete="handleEventDeleted" />

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>

            <!-- Recent Events & Activity -->
            <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Events with Filter -->
                <div class="rounded-lg border bg-card shadow-sm">
                    <div
                        class="flex flex-col space-y-1.5 p-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <h2 class="text-lg font-semibold leading-none tracking-tight">Recent Events</h2>

                        <!-- Filter Dropdown -->
                        <div class="relative">
                            <button @click="isEventFilterOpen = !isEventFilterOpen"
                                class="inline-flex items-center justify-center gap-2 rounded-md border border-input bg-background px-3 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                <Filter class="h-4 w-4" />
                                <span>{{ eventFilter }}</span>
                                <ChevronDown class="h-4 w-4 transition-transform duration-200"
                                    :class="{ 'rotate-180': isEventFilterOpen }" />
                            </button>

                            <!-- Dropdown Menu -->
                            <div v-if="isEventFilterOpen"
                                class="absolute right-0 mt-2 w-32 rounded-md border bg-popover p-1 text-popover-foreground shadow-md z-10">
                                <button v-for="option in eventFilterOptions" :key="option"
                                    @click="eventFilter = option; isEventFilterOpen = false"
                                    class="relative flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                                    :data-selected="eventFilter === option">
                                    {{ option }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Events List -->
                    <div class="p-6 pt-0">
                        <div class="space-y-4">
                            <div v-for="event in filteredRecentEvents" :key="event.id"
                                class="flex items-center justify-between rounded-lg border p-4 hover:bg-accent/50 transition-colors cursor-pointer"
                                @click="openEventDetail(event)">
                                <div class="space-y-1">
                                    <h3 class="font-medium leading-none">{{ event.title }}</h3>
                                    <p class="text-sm text-muted-foreground">
                                        {{ event.date }} • {{ event.time }} • {{ event.venue }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        :class="{
                                            'border-transparent bg-blue-500 text-primary-foreground hover:bg-blue-500/80': event.status === 'Upcoming',
                                            'border-transparent bg-emerald-500 text-primary-foreground hover:bg-emerald-500/80': event.status === 'Ongoing',
                                            'border-transparent bg-gray-500 text-primary-foreground hover:bg-gray-500/80': event.status === 'Completed',
                                        }">
                                        {{ event.status }}
                                    </span>
                                    <p class="text-xs text-muted-foreground">
                                        {{ event.registered }} registered
                                    </p>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-if="filteredRecentEvents.length === 0"
                                class="flex flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center">
                                <p class="text-sm text-muted-foreground">
                                    No {{ eventFilter.toLowerCase() }} events found
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="rounded-lg border bg-card shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h2 class="text-lg font-semibold leading-none tracking-tight">Recent Activity</h2>
                    </div>
                    <div class="p-6 pt-0">
                        <ul class="space-y-4">
                            <li v-for="(activity, index) in recentActivity" :key="index"
                                class="flex items-center justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-2 w-2 rounded-full bg-primary" />
                                    <span :class="[activity.color, 'text-sm']">{{ activity.text }}</span>
                                </div>
                                <span class="text-xs text-muted-foreground">{{ activity.time }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>