<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Plus, Users2, CalendarCheck2, CalendarDays, Award } from 'lucide-vue-next'
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    // NAVBAR TITLE
    {
        title: 'Event Management',
        href: '/event-management',
    },
];

interface EventItem {
  id: number
  title: string
  date: string
  time: string
  venue: string
  status: 'Upcoming' | 'Ongoing' | 'Completed'
  registered: number
  capacity: number
}

interface ActivityItem {
  id: number
  text: string
  timeAgo: string
}

const stats = ref([
  { label: 'Total Events', value: 24, icon: 'badge' },
  { label: 'Active Registrations', value: 187, icon: 'users' },
  { label: 'Upcoming Events', value: 8, icon: 'calendar' },
  { label: 'Average Attendance', value: '94.2%', icon: 'award' },
])

const events = ref<EventItem[]>([
  {
    id: 1,
    title: 'PSITS Tech Summit 2025',
    date: 'September 15, 2025',
    time: '9:00 AM',
    venue: 'PSITS Auditorium',
    status: 'Upcoming',
    registered: 87,
    capacity: 150,
  },
  {
    id: 2,
    title: 'Coding Bootcamp',
    date: 'September 20, 2025',
    time: '10:00 AM',
    venue: 'Computer Lab 1',
    status: 'Ongoing',
    registered: 28,
    capacity: 90,
  },
])

const activities = ref<ActivityItem[]>([
  { id: 1, text: 'New registration from Maria Santos', timeAgo: '3 hours ago' },
  { id: 2, text: 'Event details updated', timeAgo: '1 day ago' },
  { id: 3, text: 'Reminder sent to 87 attendees', timeAgo: '2 days ago' },
])

function goCreate() {
  router.visit('/events/create')
}

function openEvent(eventId: number) {
  router.visit(`/events/${eventId}`)
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen px-6 py-6 md:px-8 md:py-8">
      <Head title="Event Management" />

      <!-- Header -->
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold tracking-tight">Event Management</h1>
          <p class="text-sm md:text-base mt-2 text-muted-foreground">
            Manage and monitor PSITS events, registrations, and analytics
          </p>
        </div>

        <button
          type="button"
          @click="goCreate"
          class="inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-medium shadow-sm border hover:shadow md:px-5"
        >
          <Plus class="size-4" />
          <span>Create Event</span>
        </button>
      </div>

      <!-- Stat Cards 2x2 -->
      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-2xl border p-6 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium opacity-80">{{ stats[0].label }}</p>
              <p class="mt-3 text-4xl font-bold">{{ stats[0].value }}</p>
            </div>
            <div class="rounded-xl p-3 border opacity-70">
              <BadgeIcon />
            </div>
          </div>
        </div>

        <div class="rounded-2xl border p-6 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium opacity-80">{{ stats[1].label }}</p>
              <p class="mt-3 text-4xl font-bold">{{ stats[1].value }}</p>
            </div>
            <div class="rounded-xl p-3 border opacity-70">
              <Users2 class="size-6" />
            </div>
          </div>
        </div>

        <div class="rounded-2xl border p-6 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium opacity-80">{{ stats[2].label }}</p>
              <p class="mt-3 text-4xl font-bold">{{ stats[2].value }}</p>
            </div>
            <div class="rounded-xl p-3 border opacity-70">
              <CalendarDays class="size-6" />
            </div>
          </div>
        </div>

        <div class="rounded-2xl border p-6 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-sm font-medium opacity-80">{{ stats[3].label }}</p>
              <p class="mt-3 text-4xl font-bold">{{ stats[3].value }}</p>
            </div>
            <div class="rounded-xl p-3 border opacity-70">
              <Award class="size-6" />
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom: Recent Events + Recent Activity (2 columns) -->
      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Recent Events -->
        <section class="rounded-2xl border shadow-sm">
          <header class="flex items-center justify-between px-5 py-4 border-b">
            <h2 class="text-sm font-semibold tracking-wide">Recent Events</h2>
            <button class="rounded-full border p-2 text-xs">⟳</button>
          </header>

          <ul class="divide-y">
            <li
              v-for="e in events"
              :key="e.id"
              class="px-5 py-4 cursor-pointer hover:bg-muted/50"
              @click="openEvent(e.id)"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="font-medium truncate">{{ e.title }}</p>
                  <div class="mt-2 flex flex-wrap items-center gap-3 text-xs opacity-80">
                    <div class="flex items-center gap-1">
                      <CalendarCheck2 class="size-3.5" />
                      <span>{{ e.date }}</span>
                    </div>
                    <span>•</span>
                    <div class="flex items-center gap-1">
                      <CalendarDays class="size-3.5" />
                      <span>{{ e.time }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ e.venue }}</span>
                  </div>
                </div>

                <div class="flex flex-col items-end gap-2">
                  <span
                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide"
                  >
                    {{ e.status }}
                  </span>
                  <div class="text-xs opacity-80">{{ e.registered }}/{{ e.capacity }} registered</div>
                </div>
              </div>
            </li>
          </ul>
        </section>

        <!-- Recent Activity -->
        <section class="rounded-2xl border shadow-sm">
          <header class="px-5 py-4 border-b">
            <h2 class="text-sm font-semibold tracking-wide">Recent Activity</h2>
          </header>

          <ul class="divide-y">
            <li v-for="a in activities" :key="a.id" class="px-5 py-4">
              <div class="flex items-start justify-between gap-3">
                <p class="text-sm">{{ a.text }}</p>
                <span class="text-xs opacity-70 whitespace-nowrap">{{ a.timeAgo }}</span>
              </div>
            </li>
          </ul>
        </section>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts">
import { defineComponent, h } from 'vue'
export default { }
export const BadgeIcon = defineComponent({
  name: 'BadgeIcon',
  setup() {
    return () => h('div', { class: 'flex items-center justify-center' }, [h('svg', {
      xmlns: 'http://www.w3.org/2000/svg',
      viewBox: '0 0 24 24',
      fill: 'none',
      stroke: 'currentColor',
      'stroke-width': 2,
      class: 'size-6'
    }, [
      h('path', { d: 'M12 2l2.39 4.84L20 8l-4 3.9.94 5.48L12 15.77 7.06 17.38 8 13 4 9l5.61-.16L12 2z' })
    ])])
  }
})
</script>

<style scoped>
.text-muted-foreground { opacity: 0.8; }
</style>