<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendee', href: '/attendees' },
]

// Types
export type Attendee = {
    id?: number
    first_name: string
    middle_name?: string | null
    last_name: string
    email: string
    status: 'present' | 'absent'
    checkin_type?: 'qr' | 'manual' | null
    checkin_time?: string | null // HH:MM
    date: string // YYYY-MM-DD
}

const props = defineProps<{ attendees: Attendee[] }>()


// UI state
const showFilters = ref(false)
const showAddModal = ref(false)


// Filters & search
const search = ref('')
const filters = reactive({
    status: '' as '' | 'present' | 'absent' | 'qr' | 'manual',
    from: '' as string | '',
    to: '' as string | '',
})

// Derived list
const filtered = computed(() => {
    return props.attendees.filter(a => {
        // search filter
        const term = search.value.trim().toLowerCase()
        const matchesSearch = !term || [
        `${a.first_name} ${a.last_name}`,
        a.email,
    ].some(v => v.toLowerCase().includes(term))

    // status filter (including qr/manual pseudo-status)
    const f = filters.status
    const matchesStatus = !f
    || (f === 'present' && a.status === 'present')
    || (f === 'absent' && a.status === 'absent')
    || (f === 'qr' && a.status === 'present' && a.checkin_type === 'qr')
    || (f === 'manual' && a.status === 'present' && a.checkin_type === 'manual')

    // date range
    const fromOk = !filters.from || a.date >= filters.from
    const toOk = !filters.to || a.date <= filters.to

    return matchesSearch && matchesStatus && fromOk && toOk
    })
})

// Stats
const totalPresent = computed(() => props.attendees.filter(a => a.status === 'present').length)
const manualCheckins = computed(() => props.attendees.filter(a => a.status === 'present' && a.checkin_type === 'manual').length)
const qrCheckins = computed(() => props.attendees.filter(a => a.status === 'present' && a.checkin_type === 'qr').length)
const totalAbsent = computed(() => props.attendees.filter(a => a.status === 'absent').length)


// Add attendee form (Inertia useForm handles state + errors)
const form = useForm<Attendee>({
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
    date: '',
    status: 'present',
    checkin_type: null,
    checkin_time: '',
})


const onChangeStatus = () => {
    if (form.status === 'absent') {
        form.checkin_type = null
        form.checkin_time = ''
    }
}


const submit = () => {
// Simple front-end validation parallel to backend rules
    if (!form.first_name || !form.last_name || !form.email || !form.date || !form.status) {
        alert('Please fill in all required fields')
        return
    }
    if (form.status === 'present' && !form.checkin_type) {
        alert('Please select a check-in type')
        return
    }

    form.post(route('attendees.store'), {
        onSuccess: () => {
            showAddModal.value = false
            form.reset()
        },
    })
}

// Helpers
const initials = (a: Attendee) => {
    const parts = [a.first_name, a.last_name].filter(Boolean)
    return parts.map(p => p[0]).join('').toUpperCase()
}
</script>


<template>
    <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Attendee Management - PSITS Tech Summit 2025" />
        <div class="min-h-screen bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Header with Export Button -->
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Attendee Management</h1>
                        <p class="text-gray-600">Manage registrations and check-ins for PSITS Tech Summit 2025</p>
                    </div>
                        <Link :href="route('attendees.export')" class="px-4 py-2 border border-gray-300 rounded-md flex items-center text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-download mr-2"></i>
                        Export
                        </Link>
                    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Present</p>
                        <p class="text-2xl font-bold text-gray-900">{{ totalPresent }}</p>
                    </div>
                </div>
            </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-4">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
            <div>
                <p class="text-sm text-gray-500">Manual Check-ins</p>
                <p class="text-2xl font-bold text-gray-900">{{ manualCheckins }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-4">
                <i class="fas fa-qrcode text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">QR Check-ins</p>
                <p class="text-2xl font-bold text-gray-900">{{ qrCheckins }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mr-4">
                <i class="fas fa-user-times text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Absent</p>
                <p class="text-2xl font-bold text-gray-900">{{ totalAbsent }}</p>
            </div>
        </div>
    </div>
    <!-- Attendee List Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Attendee List</h2>
            <div class="flex flex-wrap gap-2">
                <button @click="showFilters = !showFilters" class="px-4 py-2 border border-gray-300 rounded-md flex items-center text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add Attendee
                </button>
            </div>
        </div>
    <!-- Search Bar -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="relative max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input v-model="search" type="text" placeholder="Search attendees..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    <!-- Filter Options -->
    <div v-show="showFilters" class="px-6 py-4 bg-gray-50 border-b border-gray-200 transition-all">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select v-model="filters.status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="qr">QR Check-in</option>
                    <option value="manual">Manual Check-in</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" v-model="filters.from" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" v-model="filters.to" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button @click="/* optional: trigger any filter action */" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full">
                Apply Filters
                </button>
            </div>
        </div>
    </div>
    <!-- Attendee List -->
    <div class="divide-y divide-gray-200">
        <div v-for="attendee in filtered" :key="attendee.id" class="px-6 py-4 flex items-center">
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-semibold mr-4">
                {{ initials(attendee) }}
            </div>
            <div class="flex-grow">
                <h3 class="text-base font-medium text-gray-900">{{ attendee.first_name }} {{ attendee.last_name }}</h3>
                <p class="text-sm text-gray-500">{{ attendee.email }}</p>
            </div>
            <div class="ml-4">
                <span v-if="attendee.status === 'absent'" class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-sm font-medium">Absent</span>
                <span v-else class="status-badge bg-green-100 text-green-800 text-sm font-medium flex items-center">
                <i :class="attendee.checkin_type === 'qr' ? 'fas fa-qrcode mr-1' : 'fas fa-user-check mr-1'"></i>
                Checked in at {{ attendee.checkin_time || 'N/A' }}
                </span>
            </div>
        </div>
    </div>
</div>
<!-- Add Attendee Modal -->
<div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-medium text-gray-900">Add New Attendee</h3>
        </div>
    <div class="px-6 py-4">
        <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input v-model="form.first_name" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                <input v-model="form.middle_name" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                <input v-model="form.last_name" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
            <input v-model="form.date" type="date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
            <select v-model="form.status" @change="onChangeStatus" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
            </select>
        </div>
        <div v-if="form.status === 'present'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Type *</label>
            <select v-model="form.checkin_type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="">Select type</option>
                <option value="qr">QR Check-in</option>
                <option value="manual">Manual Check-in</option>
            </select>
        </div>
        <div v-if="form.status === 'present'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Time</label>
            <input v-model="form.checkin_time" type="time" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        </form>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <button @click="showAddModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
            <button @click="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Attendee</button>
        </div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </AppLayout>
</template>