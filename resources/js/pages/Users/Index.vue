<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { PageProps as InertiaPageProps } from '@inertiajs/core'

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
    flash?: {
        success?: string
        error?: string
    }
    success?: string
    error?: string
}>()

interface PageProps extends InertiaPageProps {
    flash?: {
        success?: string
        error?: string
    }
    success?: string
    error?: string
    users?: PaginatedUsers
    roles?: Role[]
    filters?: {
        search?: string
        role?: string
        status?: string
    }
}

// Reactive data
const activeTab = ref('list')
const loading = ref(false)
const usersList = ref<PaginatedUsers | null>(props.users || null)
const rolesList = ref<Role[]>(props.roles || [])

const page = usePage<PageProps>()
const flashMessage = computed(() => page.props.flash?.success || page.props.success)
const errorMessage = computed(() => page.props.flash?.error || page.props.error)

const showFlashMessage = ref(false)

// Watch for flash messages
watch([flashMessage, errorMessage], () => {
    if (flashMessage.value || errorMessage.value) {
        showFlashMessage.value = true
        setTimeout(() => {
            showFlashMessage.value = false
        }, 5000)
    }
})

// Use Inertia form
const userForm = useForm({
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

const errors = computed(() => userForm.errors)

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

// Status options with theme-aware classes using semantic tokens
const statusOptions = [
    { value: 'active', label: 'Active', class: 'bg-success/10 text-success border-success/20' },
    { value: 'inactive', label: 'Inactive', class: 'bg-muted text-muted-foreground border-border' },
    { value: 'suspended', label: 'Suspended', class: 'bg-destructive/10 text-destructive border-destructive/20' },
    { value: 'pending', label: 'Pending', class: 'bg-warning/10 text-warning border-warning/20' }
]

const dismissFlashMessage = () => {
    showFlashMessage.value = false
}

// Computed
const filteredUsers = computed(() => {
    if (!usersList.value?.data) return []
    return usersList.value.data
})

const isEditing = computed(() => userForm.user_id !== null)

// Methods
const fetchUsers = () => {
    loading.value = true
    
    router.get('/users', {
        search: filters.value.search,
        role: filters.value.role,
        status: filters.value.status
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            usersList.value = page.props.users as PaginatedUsers
            loading.value = false
        },
        onError: (errors) => {
            console.error('Error fetching users:', errors)
            loading.value = false
        }
    })
}

const fetchRoles = () => {
    if (rolesList.value.length === 0) {
        router.get('/api/roles', {}, {
            preserveState: true,
            onSuccess: (page) => {
                const pageProps = page.props as any
                rolesList.value = pageProps.roles || []
            },
            onError: (errors) => {
                console.error('Error fetching roles:', errors)
            }
        })
    }
}

const resetForm = () => {
    userForm.reset()
    userForm.clearErrors()
}


const openEditModal = (user: User) => {
    selectedUser.value = user
    
    userForm.reset()
    userForm.clearErrors()
    
    userForm.user_id = user.user_id
    userForm.first_name = user.first_name
    userForm.last_name = user.last_name
    userForm.email = user.email
    userForm.password = ''
    userForm.password_confirmation = ''
    userForm.role_id = user.role_id
    userForm.student_id = user.student_id || ''
    userForm.department = user.department || ''
    userForm.year_level = user.year_level || ''
    userForm.contact_number = user.contact_number || ''
    userForm.account_status = user.account_status
    
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

const saveUser = () => {
    loading.value = true
    
    if (isEditing.value) {
        userForm.put(`/users/${userForm.user_id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showEditModal.value = false
                showCreateModal.value = false
                resetForm()
                loading.value = false
                fetchUsers()
            },
            onError: (errors) => {
                console.error('Error updating user:', errors)
                loading.value = false
            }
        })
    } else {
        userForm.post('/users', {
            preserveScroll: true,
            onSuccess: () => {
                showCreateModal.value = false
                resetForm()
                fetchUsers()
                loading.value = false
            },
            onError: (errors) => {
                console.error('Error creating user:', errors)
                loading.value = false
            }
        })
    }
}

const deleteUser = () => {
    if (!selectedUser.value) return
    
    loading.value = true
    
    router.delete(`/users/${selectedUser.value.user_id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            fetchUsers()
            loading.value = false
        },
        onError: (errors) => {
            console.error('Error deleting user:', errors)
            loading.value = false
        }
    })
}

const updateUserStatus = async (user: User, newStatus: string) => {
    loading.value = true
    
    router.put(`/users/${user.user_id}`, {
        account_status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            fetchUsers()
            loading.value = false
        },
        onError: (errors) => {
            console.error('Error updating user status:', errors)
            loading.value = false
        }
    })
}

const getStatusBadgeClass = (status: string) => {
    const statusOption = statusOptions.find(s => s.value === status)
    return statusOption?.class || 'bg-muted text-muted-foreground border-border'
}

const clearFilters = () => {
    filters.value = { search: '', role: '', status: '' }
    fetchUsers()
}

// Watch filters
watch(filters, () => {
    fetchUsers()
}, { deep: true })

// Lifecycle
onMounted(() => {
    if (!usersList.value) {
        fetchUsers()
    }
    fetchRoles()
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Flash message display -->
        <div 
            v-if="showFlashMessage && (flashMessage || errorMessage)"
            class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-4 px-4 py-3 rounded-lg shadow-lg transition-all duration-300"
            :class="errorMessage 
                ? 'bg-destructive/10 border border-destructive/20 text-destructive' 
                : 'bg-success/10 border border-success/20 text-success'"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg 
                        v-if="errorMessage" 
                        class="w-5 h-5 mr-2" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg 
                        v-else 
                        class="w-5 h-5 mr-2" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">{{ errorMessage || flashMessage }}</p>
                </div>
                <button 
                    @click="dismissFlashMessage"
                    class="ml-2 hover:opacity-70 transition-opacity"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Main container -->
        <div class="p-6 text-muted-foreground">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-foreground">User Management</h1>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex space-x-6 border-b border-border mb-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    class="pb-2 transition-colors"
                    :class="[
                        activeTab === tab.key
                            ? 'border-b-2 border-primary text-primary'
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Filters -->
            <div v-if="activeTab === 'list'" class="bg-card rounded-lg border p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-card-foreground">Search</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search users..."
                            class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-card-foreground">Role</label>
                        <select class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            v-model="filters.role"
                        >
                            <option value="">All Roles</option>
                            <option v-for="role in rolesList" :key="role.role_id" :value="role.role_name">
                                {{ role.role_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-card-foreground">Status</label>
                        <select class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            v-model="filters.status"
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
                            class="w-full px-4 py-2 rounded-lg transition-colors bg-secondary text-secondary-foreground hover:bg-secondary/80"
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
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        <p class="mt-2 text-muted-foreground">Loading users...</p>
                    </div>
                    
                    <!-- Users table -->
                    <div v-else-if="usersList?.data?.length" class="bg-card rounded-lg border overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-muted/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr 
                                        v-for="user in filteredUsers" 
                                        :key="user.user_id" 
                                        class="hover:bg-muted/50"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-foreground">{{ user.name }}</div>
                                                <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-card-foreground">{{ user.role_name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full border"
                                                :class="getStatusBadgeClass(user.account_status)"
                                            >
                                                {{ user.account_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">
                                            {{ user.student_id || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">
                                            {{ user.department || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button
                                                    @click="openViewModal(user)"
                                                    class="text-primary hover:text-primary/80"
                                                    title="View"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="openEditModal(user)"
                                                    class="text-warning hover:text-warning/80"
                                                    title="Edit"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="openDeleteModal(user)"
                                                    class="text-destructive hover:text-destructive/80"
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
                        <div v-if="usersList?.last_page > 1" class="px-6 py-3 bg-muted/50 flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <button class="relative inline-flex items-center px-4 py-2 border border-input text-sm font-medium rounded-md bg-background text-foreground hover:bg-muted/50 transition-colors">
                                    Previous
                                </button>
                                <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-input text-sm font-medium rounded-md bg-background text-foreground hover:bg-muted/50 transition-colors">
                                    Next
                                </button>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Showing {{ usersList.from }} to {{ usersList.to }} of {{ usersList.total }} results
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8 text-muted-foreground">
                        <p>No users found.</p>
                    </div>
                </div>

                <!-- Create User Tab -->
                <div v-if="activeTab === 'create'" class="bg-card rounded-lg border p-6">
                    <h2 class="text-xl font-semibold mb-6 text-card-foreground">Add New User</h2>
                    
                    <form @submit.prevent="saveUser" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">First Name *</label>
                                <input
                                    v-model="userForm.first_name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.first_name }"
                                >
                                <p v-if="errors.first_name" class="text-destructive text-sm mt-1">{{ errors.first_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Last Name *</label>
                                <input
                                    v-model="userForm.last_name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.last_name }"
                                >
                                <p v-if="errors.last_name" class="text-destructive text-sm mt-1">{{ errors.last_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Email *</label>
                                <input
                                    v-model="userForm.email"
                                    type="email"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.email }"
                                >
                                <p v-if="errors.email" class="text-destructive text-sm mt-1">{{ errors.email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Role *</label>
                                <select
                                    v-model="userForm.role_id"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.role_id }"
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="role in rolesList" :key="role.role_id" :value="role.role_id">
                                        {{ role.role_name }}
                                    </option>
                                </select>
                                <p v-if="errors.role_id" class="text-destructive text-sm mt-1">{{ errors.role_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Password *</label>
                                <input
                                    v-model="userForm.password"
                                    type="password"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.password }"
                                >
                                <p v-if="errors.password" class="text-destructive text-sm mt-1">{{ errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Confirm Password *</label>
                                <input
                                    v-model="userForm.password_confirmation"
                                    type="password"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Student ID</label>
                                <input
                                    v-model="userForm.student_id"
                                    type="text"
                                    placeholder="e.g., 2024-123456"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.student_id }"
                                >
                                <p v-if="errors.student_id" class="text-destructive text-sm mt-1">{{ errors.student_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Department</label>
                                <input
                                    v-model="userForm.department"
                                    type="text"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Year Level</label>
                                <select
                                    v-model="userForm.year_level"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                                    <option value="">Select Year Level</option>
                                    <option v-for="year in yearLevels" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Contact Number</label>
                                <input
                                    v-model="userForm.contact_number"
                                    type="text"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Account Status</label>
                                <select
                                    v-model="userForm.account_status"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
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
                                class="px-4 py-2 border border-input rounded-lg bg-background text-foreground hover:bg-muted/50 transition-colors"
                            >
                                Reset
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 disabled:opacity-50 transition-colors"
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
                        <div class="bg-card rounded-lg border p-6">
                            <h3 class="text-lg font-semibold mb-2 text-card-foreground">Total Users</h3>
                            <p class="text-3xl font-bold text-primary">{{ usersList?.total || 0 }}</p>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <h3 class="text-lg font-semibold mb-2 text-card-foreground">Active Users</h3>
                            <p class="text-3xl font-bold text-success">
                                {{ usersList?.data?.filter(u => u.account_status === 'active').length || 0 }}
                            </p>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <h3 class="text-lg font-semibold mb-2 text-card-foreground">Pending Users</h3>
                            <p class="text-3xl font-bold text-warning">
                                {{ usersList?.data?.filter(u => u.account_status === 'pending').length || 0 }}
                            </p>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <h3 class="text-lg font-semibold mb-2 text-card-foreground">Suspended Users</h3>
                            <p class="text-3xl font-bold text-destructive">
                                {{ usersList?.data?.filter(u => u.account_status === 'suspended').length || 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create/Edit Modal -->
            <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-card rounded-lg border p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-card-foreground">
                            {{ isEditing ? 'Edit User' : 'Create User' }}
                        </h2>
                        <button
                            @click="showCreateModal = false; showEditModal = false"
                            class="text-muted-foreground hover:text-foreground"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveUser" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">First Name *</label>
                                <input
                                    v-model="userForm.first_name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.first_name }"
                                >
                                <p v-if="errors.first_name" class="text-destructive text-sm mt-1">{{ errors.first_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Last Name *</label>
                                <input
                                    v-model="userForm.last_name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.last_name }"
                                >
                                <p v-if="errors.last_name" class="text-destructive text-sm mt-1">{{ errors.last_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Email *</label>
                                <input
                                    v-model="userForm.email"
                                    type="email"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.email }"
                                >
                                <p v-if="errors.email" class="text-destructive text-sm mt-1">{{ errors.email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Role *</label>
                                <select
                                    v-model="userForm.role_id"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.role_id }"
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="role in rolesList" :key="role.role_id" :value="role.role_id">
                                        {{ role.role_name }}
                                    </option>
                                </select>
                                <p v-if="errors.role_id" class="text-destructive text-sm mt-1">{{ errors.role_id }}</p>
                            </div>

                            <!-- Password fields for create mode -->
                            <div v-if="!isEditing">
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Password *</label>
                                <input
                                    v-model="userForm.password"
                                    type="password"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.password }"
                                >
                                <p v-if="errors.password" class="text-destructive text-sm mt-1">{{ errors.password }}</p>
                            </div>

                            <div v-if="!isEditing">
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Confirm Password *</label>
                                <input
                                    v-model="userForm.password_confirmation"
                                    type="password"
                                    required
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <!-- Password fields for edit mode -->
                            <div v-if="isEditing">
                                <label class="block text-sm font-medium mb-2 text-card-foreground">New Password</label>
                                <input
                                    v-model="userForm.password"
                                    type="password"
                                    placeholder="Leave blank to keep current password"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.password }"
                                >
                                <p v-if="errors.password" class="text-destructive text-sm mt-1">{{ errors.password }}</p>
                            </div>

                            <div v-if="isEditing && userForm.password">
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Confirm New Password</label>
                                <input
                                    v-model="userForm.password_confirmation"
                                    type="password"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Student ID</label>
                                <input
                                    v-model="userForm.student_id"
                                    type="text"
                                    placeholder="e.g., 2024-123456"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    :class="{ 'border-destructive': errors.student_id }"
                                >
                                <p v-if="errors.student_id" class="text-destructive text-sm mt-1">{{ errors.student_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Department</label>
                                <input
                                    v-model="userForm.department"
                                    type="text"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Year Level</label>
                                <select
                                    v-model="userForm.year_level"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                                    <option value="">Select Year Level</option>
                                    <option v-for="year in yearLevels" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Contact Number</label>
                                <input
                                    v-model="userForm.contact_number"
                                    type="text"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2 text-card-foreground">Account Status</label>
                                <select
                                    v-model="userForm.account_status"
                                    class="w-full rounded-lg px-3 py-2 border border-input bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
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
                                class="px-4 py-2 border border-input rounded-lg bg-background text-foreground hover:bg-muted/50 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 disabled:opacity-50 transition-colors"
                            >
                                <span v-if="loading">{{ isEditing ? 'Updating...' : 'Creating...' }}</span>
                                <span v-else>{{ isEditing ? 'Update User' : 'Create User' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View User Modal -->
            <div v-if="showViewModal && selectedUser" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-card rounded-lg border p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-card-foreground">User Details</h2>
                        <button
                            @click="showViewModal = false"
                            class="text-muted-foreground hover:text-foreground"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Full Name</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Email</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Role</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.role_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Status</label>
                                <div class="bg-muted/50 rounded-lg px-3 py-2">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full border"
                                        :class="getStatusBadgeClass(selectedUser.account_status)"
                                    >
                                        {{ selectedUser.account_status }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Student ID</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.student_id || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Department</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.department || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Year Level</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.year_level || 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1 text-card-foreground">Contact Number</label>
                                <p class="bg-muted/50 rounded-lg px-3 py-2 text-foreground">{{ selectedUser.contact_number || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Status Update Buttons -->
                        <div v-if="selectedUser.account_status !== 'active'" class="pt-4">
                            <h3 class="text-sm font-medium mb-2 text-card-foreground">Quick Actions</h3>
                            <div class="flex space-x-2">
                                <button
                                    v-if="selectedUser.account_status === 'pending'"
                                    @click="updateUserStatus(selectedUser, 'active')"
                                    class="px-3 py-1 bg-success text-success-foreground text-sm rounded hover:bg-success/90 transition-colors"
                                >
                                    Approve User
                                </button>
                                <button
                                    v-if="selectedUser.account_status === 'suspended'"
                                    @click="updateUserStatus(selectedUser, 'active')"
                                    class="px-3 py-1 bg-success text-success-foreground text-sm rounded hover:bg-success/90 transition-colors"
                                >
                                    Reactivate User
                                </button>
                                <button
                                    v-if="selectedUser.account_status !== 'suspended'"
                                    @click="updateUserStatus(selectedUser, 'suspended')"
                                    class="px-3 py-1 bg-destructive text-destructive-foreground text-sm rounded hover:bg-destructive/90 transition-colors"
                                >
                                    Suspend User
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <button
                                @click="showViewModal = false"
                                class="px-4 py-2 border border-input rounded-lg bg-background text-foreground hover:bg-muted/50 transition-colors"
                            >
                                Close
                            </button>
                            <button
                                @click="showViewModal = false; openEditModal(selectedUser)"
                                class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                            >
                                Edit User
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div v-if="showDeleteModal && selectedUser" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-card rounded-lg border p-6 w-full max-w-md">
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-destructive mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-card-foreground">Delete User</h3>
                    </div>
                    
                    <p class="mb-6 text-card-foreground">
                        Are you sure you want to delete <strong>{{ selectedUser.name }}</strong>? 
                        This action cannot be undone.
                    </p>
                    
                    <div class="flex justify-end space-x-4">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 border border-input rounded-lg bg-background text-foreground hover:bg-muted/50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="deleteUser"
                            :disabled="loading"
                            class="px-4 py-2 bg-destructive text-destructive-foreground rounded-lg hover:bg-destructive/90 disabled:opacity-50 transition-colors"
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
/* Remove all the complex theme-aware styling - Tailwind handles this automatically */
input:focus,
select:focus,
button:focus {
    outline: none;
}
</style>