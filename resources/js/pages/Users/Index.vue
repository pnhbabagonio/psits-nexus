<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'

// Define types
interface Role {
    role_id: number
    role_name: string
}

interface User {
    user_id: number
    first_name: string
    last_name: string
    email: string
    student_id?: string
    role_id: number
    role?: Role
    department?: string
    year_level?: string
    contact_number?: string
    account_status: 'active' | 'inactive' | 'suspended' | 'pending'
    date_registered?: string
    last_login?: string
    name: string
    role_name: string
    status: string
}

interface PaginatedUsers {
    data: User[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
}

// Props
const props = defineProps<{
    users?: PaginatedUsers
    roles?: Role[]
    filters?: {
        search?: string
        role?: string
        status?: string
    }
}>()

// Reactive data
const activeTab = ref('list')
const loading = ref(false)
const users = ref<PaginatedUsers | null>(props.users || null)
const roles = ref<Role[]>(props.roles || [])

// Form data
const userForm = ref({
    user_id: null as number | null,
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: null as number | null,
    student_id: '',
    department: '',
    year_level: '',
    contact_number: '',
    account_status: 'pending' as 'active' | 'inactive' | 'suspended' | 'pending'
})

// Filters
const filters = ref({
    search: props.filters?.search || '',
    role: props.filters?.role || '',
    status: props.filters?.status || ''
})

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showViewModal = ref(false)
const selectedUser = ref<User | null>(null)

// Form errors
const errors = ref<Record<string, string>>({})

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'User Management', href: '/users' }
]

// Tabs
const tabs = [
    { key: 'list', label: 'Users List' },
    { key: 'create', label: 'Add User' },
    { key: 'analytics', label: 'User Analytics' }
]

// Year level options
const yearLevels = [
    '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', 'Graduate'
]

// Status options
const statusOptions = [
    { value: 'active', label: 'Active', class: 'bg-green-100 text-green-800' },
    { value: 'inactive', label: 'Inactive', class: 'bg-gray-100 text-gray-800' },
    { value: 'suspended', label: 'Suspended', class: 'bg-red-100 text-red-800' },
    { value: 'pending', label: 'Pending', class: 'bg-yellow-100 text-yellow-800' }
]

// Computed
const filteredUsers = computed(() => {
    if (!users.value?.data) return []
    return users.value.data
})

const isEditing = computed(() => userForm.value.user_id !== null)

// Methods
const fetchUsers = async () => {
    loading.value = true
    try {
        const params = new URLSearchParams()
        if (filters.value.search) params.append('search', filters.value.search)
        if (filters.value.role) params.append('role', filters.value.role)
        if (filters.value.status) params.append('status', filters.value.status)
        
        const response = await fetch(`/users?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        
        if (response.ok) {
            users.value = await response.json()
        }
    } catch (error) {
        console.error('Error fetching users:', error)
    } finally {
        loading.value = false
    }
}

const fetchRoles = async () => {
    if (roles.value.length === 0) {
        try {
            // This would need to be added to your routes if not already available
            const response = await fetch('/api/roles', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            if (response.ok) {
                roles.value = await response.json()
            }
        } catch (error) {
            console.error('Error fetching roles:', error)
        }
    }
}

const resetForm = () => {
    userForm.value = {
        user_id: null,
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: null,
        student_id: '',
        department: '',
        year_level: '',
        contact_number: '',
        account_status: 'pending'
    }
    errors.value = {}
}

const openCreateModal = () => {
    resetForm()
    showCreateModal.value = true
    activeTab.value = 'list'
}

const openEditModal = (user: User) => {
    selectedUser.value = user
    userForm.value = {
        user_id: user.user_id,
        first_name: user.first_name,
        last_name: user.last_name,
        email: user.email,
        password: '',
        password_confirmation: '',
        role_id: user.role_id,
        student_id: user.student_id || '',
        department: user.department || '',
        year_level: user.year_level || '',
        contact_number: user.contact_number || '',
        account_status: user.account_status
    }
    showEditModal.value = true
}

const openViewModal = (user: User) => {
    selectedUser.value = user
    showViewModal.value = true
}

const openDeleteModal = (user: User) => {
    selectedUser.value = user
    showDeleteModal.value = true
}

const saveUser = async () => {
    loading.value = true
    errors.value = {}
    
    try {
        const url = isEditing.value ? `/users/${userForm.value.user_id}` : '/users'
        const method = isEditing.value ? 'PUT' : 'POST'
        
        // Create a copy of the form data
        const formData: Record<string, any> = { ...userForm.value }
        
        // Remove password fields if editing and password is empty
        if (isEditing.value && !formData.password) {
            delete formData.password
            delete formData.password_confirmation
        }
        
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(formData)
        })
        
        if (response.ok) {
            showCreateModal.value = false
            showEditModal.value = false
            await fetchUsers()
            resetForm()
        } else {
            const errorData = await response.json()
            if (errorData.errors) {
                errors.value = errorData.errors
            }
        }
    } catch (error) {
        console.error('Error saving user:', error)
    } finally {
        loading.value = false
    }
}

const deleteUser = async () => {
    if (!selectedUser.value) return
    
    loading.value = true
    try {
        const response = await fetch(`/users/${selectedUser.value.user_id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        
        if (response.ok) {
            showDeleteModal.value = false
            await fetchUsers()
        }
    } catch (error) {
        console.error('Error deleting user:', error)
    } finally {
        loading.value = false
    }
}

const updateUserStatus = async (user: User, newStatus: string) => {
    loading.value = true
    try {
        const response = await fetch(`/users/${user.user_id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ account_status: newStatus })
        })
        
        if (response.ok) {
            await fetchUsers()
        }
    } catch (error) {
        console.error('Error updating user status:', error)
    } finally {
        loading.value = false
    }
}

const getStatusBadgeClass = (status: string) => {
    const statusOption = statusOptions.find(s => s.value === status)
    return statusOption?.class || 'bg-gray-100 text-gray-800'
}

const clearFilters = () => {
    filters.value = { search: '', role: '', status: '' }
    fetchUsers()
}

// Watch filters for real-time search
watch(filters, () => {
    fetchUsers()
}, { deep: true })

// Lifecycle
onMounted(() => {
    if (!users.value) {
        fetchUsers()
    }
    fetchRoles()
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 text-gray-200">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-white">User Management</h1>
                <button
                    @click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add User</span>
                </button>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex space-x-6 border-b border-gray-800 mb-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    class="pb-2 transition-colors"
                    :class="[
                        activeTab === tab.key
                            ? 'border-b-2 border-blue-500 text-blue-400'
                            : 'text-gray-400 hover:text-gray-200'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Filters (only show on list tab) -->
            <div v-if="activeTab === 'list'" class="bg-gray-800 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search users..."
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <select
                            v-model="filters.role"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Roles</option>
                            <option v-for="role in roles" :key="role.role_id" :value="role.role_name">
                                {{ role.role_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select
                            v-model="filters.status"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div>
                <!-- Users List Tab -->
                <div v-if="activeTab === 'list'">
                    <div v-if="loading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        <p class="mt-2 text-gray-400">Loading users...</p>
                    </div>
                    
                    <div v-else-if="users?.data?.length" class="bg-gray-800 rounded-lg overflow-hidden">
                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    <tr v-for="user in filteredUsers" :key="user.user_id" class="hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ user.name }}</div>
                                                <div class="text-sm text-gray-400">{{ user.email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-300">{{ user.role_name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getStatusBadgeClass(user.account_status)"
                                            >
                                                {{ user.account_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ user.student_id || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ user.department || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button
                                                    @click="openViewModal(user)"
                                                    class="text-blue-400 hover:text-blue-300"
                                                    title="View"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="openEditModal(user)"
                                                    class="text-yellow-400 hover:text-yellow-300"
                                                    title="Edit"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="openDeleteModal(user)"
                                                    class="text-red-400 hover:text-red-300"
                                                    title="Delete"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="users?.last_page > 1" class="bg-gray-700 px-6 py-3 flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <button class="relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                    Previous
                                </button>
                                <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700">
                                    Next
                                </button>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-400">
                                        Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                                    </p>
                                </div>
                                <div class="flex space-x-1">
                                    <!-- Pagination buttons would go here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8 text-gray-400">
                        <p>No users found.</p>
                    </div>
                </div>

                <!-- Create User Tab -->
                <div v-if="activeTab === 'create'" class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-white mb-6">Add New User</h2>
                    
                    <form @submit.prevent="saveUser" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">First Name *</label>
                                <input
                                    v-model="userForm.first_name"
                                    type="text"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.first_name }"
                                >
                                <p v-if="errors.first_name" class="text-red-400 text-sm mt-1">{{ errors.first_name[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Last Name *</label>
                                <input
                                    v-model="userForm.last_name"
                                    type="text"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.last_name }"
                                >
                                <p v-if="errors.last_name" class="text-red-400 text-sm mt-1">{{ errors.last_name[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                                <input
                                    v-model="userForm.email"
                                    type="email"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.email }"
                                >
                                <p v-if="errors.email" class="text-red-400 text-sm mt-1">{{ errors.email[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Role *</label>
                                <select
                                    v-model="userForm.role_id"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.role_id }"
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="role in roles" :key="role.role_id" :value="role.role_id">
                                        {{ role.role_name }}
                                    </option>
                                </select>
                                <p v-if="errors.role_id" class="text-red-400 text-sm mt-1">{{ errors.role_id[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Password *</label>
                                <input
                                    v-model="userForm.password"
                                    type="password"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.password }"
                                >
                                <p v-if="errors.password" class="text-red-400 text-sm mt-1">{{ errors.password[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password *</label>
                                <input
                                    v-model="userForm.password_confirmation"
                                    type="password"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Student ID</label>
                                <input
                                    v-model="userForm.student_id"
                                    type="text"
                                    placeholder="e.g., 2024-123456"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.student_id }"
                                >
                                <p v-if="errors.student_id" class="text-red-400 text-sm mt-1">{{ errors.student_id[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Department</label>
                                <input
                                    v-model="userForm.department"
                                    type="text"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Year Level</label>
                                <select
                                    v-model="userForm.year_level"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Select Year Level</option>
                                    <option v-for="year in yearLevels" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Contact Number</label>
                                <input
                                    v-model="userForm.contact_number"
                                    type="text"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Account Status</label>
                                <select
                                    v-model="userForm.account_status"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                        {{ status.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button
                                type="button"
                                @click="resetForm"
                                class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors"
                            >
                                Reset
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                            >
                                <span v-if="loading">Creating...</span>
                                <span v-else>Create User</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Analytics Tab -->
                <div v-if="activeTab === 'analytics'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-white mb-2">Total Users</h3>
                            <p class="text-3xl font-bold text-blue-400">{{ users?.total || 0 }}</p>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-white mb-2">Active Users</h3>
                            <p class="text-3xl font-bold text-green-400">
                                {{ users?.data?.filter(u => u.account_status === 'active').length || 0 }}
                            </p>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-white mb-2">Pending Users</h3>
                            <p class="text-3xl font-bold text-yellow-400">
                                {{ users?.data?.filter(u => u.account_status === 'pending').length || 0 }}
                            </p>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-white mb-2">Suspended Users</h3>
                            <p class="text-3xl font-bold text-red-400">
                                {{ users?.data?.filter(u => u.account_status === 'suspended').length || 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create/Edit Modal -->
            <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-white">
                            {{ isEditing ? 'Edit User' : 'Create User' }}
                        </h2>
                        <button
                            @click="showCreateModal = false; showEditModal = false"
                            class="text-gray-400 hover:text-white"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveUser" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">First Name *</label>
                                <input
                                    v-model="userForm.first_name"
                                    type="text"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.first_name }"
                                >
                                <p v-if="errors.first_name" class="text-red-400 text-sm mt-1">{{ errors.first_name[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Last Name *</label>
                                <input
                                    v-model="userForm.last_name"
                                    type="text"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.last_name }"
                                >
                                <p v-if="errors.last_name" class="text-red-400 text-sm mt-1">{{ errors.last_name[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                                <input
                                    v-model="userForm.email"
                                    type="email"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.email }"
                                >
                                <p v-if="errors.email" class="text-red-400 text-sm mt-1">{{ errors.email[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Role *</label>
                                <select
                                    v-model="userForm.role_id"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.role_id }"
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="role in roles" :key="role.role_id" :value="role.role_id">
                                        {{ role.role_name }}
                                    </option>
                                </select>
                                <p v-if="errors.role_id" class="text-red-400 text-sm mt-1">{{ errors.role_id[0] }}</p>
                            </div>

                            <div v-if="!isEditing">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Password *</label>
                                <input
                                    v-model="userForm.password"
                                    type="password"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.password }"
                                >
                                <p v-if="errors.password" class="text-red-400 text-sm mt-1">{{ errors.password[0] }}</p>
                            </div>

                            <div v-if="!isEditing">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password *</label>
                                <input
                                    v-model="userForm.password_confirmation"
                                    type="password"
                                    required
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div v-if="isEditing">
                                <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                                <input
                                    v-model="userForm.password"
                                    type="password"
                                    placeholder="Leave blank to keep current password"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.password }"
                                >
                                <p v-if="errors.password" class="text-red-400 text-sm mt-1">{{ errors.password[0] }}</p>
                            </div>

                            <div v-if="isEditing && userForm.password">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                                <input
                                    v-model="userForm.password_confirmation"
                                    type="password"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Student ID</label>
                                <input
                                    v-model="userForm.student_id"
                                    type="text"
                                    placeholder="e.g., 2024-123456"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.student_id }"
                                >
                                <p v-if="errors.student_id" class="text-red-400 text-sm mt-1">{{ errors.student_id[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Department</label>
                                <input
                                    v-model="userForm.department"
                                    type="text"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Year Level</label>
                                <select
                                    v-model="userForm.year_level"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Select Year Level</option>
                                    <option v-for="year in yearLevels" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Contact Number</label>
                                <input
                                    v-model="userForm.contact_number"
                                    type="text"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Account Status</label>
                                <select
                                    v-model="userForm.account_status"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                        {{ status.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <button
                                type="button"
                                @click="showCreateModal = false; showEditModal = false"
                                class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                            >
                                <span v-if="loading">{{ isEditing ? 'Updating...' : 'Creating...' }}</span>
                                <span v-else>{{ isEditing ? 'Update User' : 'Create User' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View User Modal -->
            <div v-if="showViewModal && selectedUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-white">User Details</h2>
                        <button
                            @click="showViewModal = false"
                            class="text-gray-400 hover:text-white"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.role_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                                <p class="bg-gray-700 rounded-lg px-3 py-2">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="getStatusBadgeClass(selectedUser.account_status)"
                                    >
                                        {{ selectedUser.account_status }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Student ID</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.student_id || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Department</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.department || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Year Level</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.year_level || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Contact Number</label>
                                <p class="text-white bg-gray-700 rounded-lg px-3 py-2">{{ selectedUser.contact_number || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Status Update Buttons -->
                        <div v-if="selectedUser.account_status !== 'active'" class="pt-4">
                            <h3 class="text-sm font-medium text-gray-300 mb-2">Quick Actions</h3>
                            <div class="flex space-x-2">
                                <button
                                    v-if="selectedUser.account_status === 'pending'"
                                    @click="updateUserStatus(selectedUser, 'active')"
                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors"
                                >
                                    Approve User
                                </button>
                                <button
                                    v-if="selectedUser.account_status === 'suspended'"
                                    @click="updateUserStatus(selectedUser, 'active')"
                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors"
                                >
                                    Reactivate User
                                </button>
                                <button
                                    v-if="selectedUser.account_status !== 'suspended'"
                                    @click="updateUserStatus(selectedUser, 'suspended')"
                                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors"
                                >
                                    Suspend User
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <button
                                @click="showViewModal = false"
                                class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors"
                            >
                                Close
                            </button>
                            <button
                                @click="showViewModal = false; openEditModal(selectedUser)"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                Edit User
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal && selectedUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Delete User</h3>
                    </div>
                    
                    <p class="text-gray-300 mb-6">
                        Are you sure you want to delete <strong>{{ selectedUser.name }}</strong>? 
                        This action cannot be undone.
                    </p>
                    
                    <div class="flex justify-end space-x-4">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteUser"
                            :disabled="loading"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors"
                        >
                            <span v-if="loading">Deleting...</span>
                            <span v-else>Delete User</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom scrollbar for modals */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #374151;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #6B7280;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}
</style>