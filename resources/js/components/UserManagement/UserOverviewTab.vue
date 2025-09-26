<script setup lang="ts">
import { computed } from 'vue'

interface User {
    id: number
    name: string
    email: string
    student_id: string
    program: string
    year: string
    role: 'Member' | 'Officer' | 'Admin'
    status: 'active' | 'inactive'
    last_login: string
}

interface UserStats {
    total: number
    active: number
    inactive: number
    admins: number
    officers: number
    members: number
}

const props = defineProps<{
    users: User[]
    stats: UserStats
}>()

const emit = defineEmits<{
    switchToUsers: []
    switchToAdd: []
}>()

// Recent users (last 5)
const recentUsers = computed(() => {
    return props.users.slice(0, 5)
})

// Role distribution for chart
const roleDistribution = computed(() => {
    const total = props.stats.total
    return [
        { name: 'Members', value: props.stats.members, percentage: total ? Math.round((props.stats.members / total) * 100) : 0, color: 'bg-green-500' },
        { name: 'Officers', value: props.stats.officers, percentage: total ? Math.round((props.stats.officers / total) * 100) : 0, color: 'bg-blue-500' },
        { name: 'Admins', value: props.stats.admins, percentage: total ? Math.round((props.stats.admins / total) * 100) : 0, color: 'bg-red-500' },
    ]
})

const getRoleColor = (role: string) => {
    switch (role) {
        case 'Admin': return 'bg-red-900/50 text-red-200 border-red-700'
        case 'Officer': return 'bg-blue-900/50 text-blue-200 border-blue-700'
        case 'Member': return 'bg-green-900/50 text-green-200 border-green-700'
        default: return 'bg-gray-900/50 text-gray-200 border-gray-700'
    }
}

const getStatusColor = (status: string) => {
    return status === 'active' 
        ? 'bg-green-900/50 text-green-200 border-green-700'
        : 'bg-red-900/50 text-red-200 border-red-700'
}
</script>

<template>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Total Users</div>
                        <div class="text-2xl font-bold text-white">{{ stats.total }}</div>
                    </div>
                    <div class="w-8 h-8 bg-blue-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Active</div>
                        <div class="text-2xl font-bold text-green-400">{{ stats.active }}</div>
                    </div>
                    <div class="w-8 h-8 bg-green-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Inactive</div>
                        <div class="text-2xl font-bold text-red-400">{{ stats.inactive }}</div>
                    </div>
                    <div class="w-8 h-8 bg-red-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Admins</div>
                        <div class="text-2xl font-bold text-red-400">{{ stats.admins }}</div>
                    </div>
                    <div class="w-8 h-8 bg-red-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Officers</div>
                        <div class="text-2xl font-bold text-blue-400">{{ stats.officers }}</div>
                    </div>
                    <div class="w-8 h-8 bg-blue-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-400">Members</div>
                        <div class="text-2xl font-bold text-green-400">{{ stats.members }}</div>
                    </div>
                    <div class="w-8 h-8 bg-green-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Role Distribution -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4">Role Distribution</h3>
                <div class="space-y-3">
                    <div v-for="role in roleDistribution" :key="role.name" class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full" :class="role.color"></div>
                            <span class="text-gray-300">{{ role.name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-white font-medium">{{ role.value }}</span>
                            <span class="text-gray-400 text-sm">({{ role.percentage }}%)</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full flex">
                        <div 
                            v-for="role in roleDistribution" 
                            :key="role.name"
                            :class="role.color"
                            :style="{ width: role.percentage + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button 
                        @click="emit('switchToAdd')"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg flex items-center gap-3 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New User
                    </button>
                    <button 
                        @click="emit('switchToUsers')"
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex items-center gap-3 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        View All Users
                    </button>
                    <button class="w-full bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex items-center gap-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export User Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Users</h3>
                <button 
                    @click="emit('switchToUsers')"
                    class="text-blue-400 hover:text-blue-300 text-sm transition-colors"
                >
                    View All
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-2 text-sm font-medium text-gray-400">Name</th>
                            <th class="text-left py-2 text-sm font-medium text-gray-400">Email</th>
                            <th class="text-left py-2 text-sm font-medium text-gray-400">Role</th>
                            <th class="text-left py-2 text-sm font-medium text-gray-400">Status</th>
                            <th class="text-left py-2 text-sm font-medium text-gray-400">Last Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in recentUsers" :key="user.id" class="border-b border-gray-700/50">
                            <td class="py-3">
                                <div class="text-white font-medium">{{ user.name }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-gray-300">{{ user.email }}</div>
                            </td>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getRoleColor(user.role)">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getStatusColor(user.status)">
                                    {{ user.status }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="text-gray-300">{{ user.last_login }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>