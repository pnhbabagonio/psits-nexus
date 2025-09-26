<script setup lang="ts">
import { ref, computed } from 'vue'
import EditUserModal from './EditUserModal.vue'
import DeleteUserModal from './DeleteUserModal.vue'

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

const props = defineProps<{
    users: User[]
}>()

// State
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref<User | null>(null)
const searchQuery = ref("")
const roleFilter = ref("all")
const statusFilter = ref("all")
const sortField = ref("id")
const sortDirection = ref("desc")

// Computed
const filteredAndSortedUsers = computed(() => {
    let filtered = props.users.filter(user => {
        const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            user.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            user.student_id.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                            user.program.toLowerCase().includes(searchQuery.value.toLowerCase())
        
        const matchesRole = roleFilter.value === 'all' || user.role === roleFilter.value
        const matchesStatus = statusFilter.value === 'all' || user.status === statusFilter.value
        
        return matchesSearch && matchesRole && matchesStatus
    })

    // Sort
    return filtered.sort((a, b) => {
        const aValue = a[sortField.value as keyof User]
        const bValue = b[sortField.value as keyof User]
        
        if (sortDirection.value === 'asc') {
            return aValue > bValue ? 1 : -1
        } else {
            return aValue < bValue ? 1 : -1
        }
    })
})

// Methods
const openEditModal = (user: User) => {
    selectedUser.value = user
    showEditModal.value = true
}

const openDeleteModal = (user: User) => {
    selectedUser.value = user
    showDeleteModal.value = true
}

const closeEditModal = () => {
    showEditModal.value = false
    selectedUser.value = null
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedUser.value = null
}

const sortBy = (field: string) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortField.value = field
        sortDirection.value = 'asc'
    }
}

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

const getSortIcon = (field: string) => {
    if (sortField.value !== field) {
        return '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>'
    }
    
    return sortDirection.value === 'asc' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>'
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header with filters -->
        <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
            <div>
                <h2 class="text-xl font-semibold text-white">All Users</h2>
                <p class="text-gray-400">{{ filteredAndSortedUsers.length }} users found</p>
            </div>
            
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search users..."
                        class="pl-10 pr-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 w-full sm:w-64"
                    />
                </div>
                
                <select
                    v-model="roleFilter"
                    class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-500"
                >
                    <option value="all">All Roles</option>
                    <option value="Admin">Admin</option>
                    <option value="Officer">Officer</option>
                    <option value="Member">Member</option>
                </select>
                
                <select
                    v-model="statusFilter"
                    class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-blue-500"
                >
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('name')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Name
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('name')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('email')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Email
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('email')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-300">Student ID</th>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('program')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Program
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('program')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('year')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Year
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('year')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('role')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Role
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('role')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('status')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Status
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('status')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3">
                                <button 
                                    @click="sortBy('last_login')"
                                    class="flex items-center gap-2 text-sm font-medium text-gray-300 hover:text-white transition-colors"
                                >
                                    Last Login
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <g v-html="getSortIcon('last_login')"></g>
                                    </svg>
                                </button>
                            </th>
                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="user in filteredAndSortedUsers" :key="user.id" class="hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-900/50 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-200">{{ user.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ user.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ user.student_id || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ user.program || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ user.year || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getRoleColor(user.role)">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border" :class="getStatusColor(user.status)">
                                    {{ user.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ user.last_login }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button 
                                        @click="openEditModal(user)"
                                        class="text-blue-400 hover:text-blue-300 p-1 rounded transition-colors"
                                        title="Edit User"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="openDeleteModal(user)"
                                        class="text-red-400 hover:text-red-300 p-1 rounded transition-colors"
                                        title="Delete User"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="filteredAndSortedUsers.length === 0">
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-400 mb-1">No users found</h3>
                                    <p class="text-gray-500">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <EditUserModal 
        v-if="showEditModal && selectedUser" 
        :user="selectedUser"
        @close="closeEditModal"
    />
    
    <DeleteUserModal 
        v-if="showDeleteModal && selectedUser" 
        :user="selectedUser"
        @close="closeDeleteModal"
    />
</template>