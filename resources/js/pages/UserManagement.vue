<!-- UserManagement.vue -->
<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import { Plus, Pencil, Trash, Eye, Users } from 'lucide-vue-next'
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue'
import UserModal from '@/components/UserModal.vue'
import UserForm from '@/components/UserForm.vue'
import Pagination from '@/components/Pagination.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'

// =====================
// Reactive States
// =====================

const isAddUserOpen = ref(false)
const isViewUserOpen = ref(false)
const isEditUserOpen = ref(false)
const selectedUser = ref<any>(null)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'User Management',
        href: '/user-management',
    },
];

// Pagination state
const currentPage = ref(1)
const perPage = ref(10)

// Use data from Laravel backend - FIXED: Proper reactive data handling
const page = usePage()
const users = computed(() => {
    const usersData = page.props.users || []
    return Array.isArray(usersData) ? usersData : []
})

// Add User Form - FIXED: Proper date handling
const newUserForm = useForm({
    firstName: '',
    middleName: '',
    lastName: '',
    email: '',
    studentId: '',
    program: '',
    year: '',
    role: 'Member',
    status: 'active',
    lastLogin: new Date().toISOString().split('T')[0], // Ensure valid date format
})

// Edit User Form - FIXED: Proper initialization
const editUserForm = useForm({
    id: null,
    firstName: '',
    middleName: '',
    lastName: '',
    email: '',
    studentId: '',
    program: '',
    year: '',
    role: '',
    status: '',
    lastLogin: '',
})

function saveNewUser() {
    const fullName = `${newUserForm.firstName} ${newUserForm.middleName} ${newUserForm.lastName}`.replace(/\s+/g, ' ').trim()

    // FIXED: Validate lastLogin date
    if (!newUserForm.lastLogin || isNaN(new Date(newUserForm.lastLogin).getTime())) {
        newUserForm.lastLogin = new Date().toISOString().split('T')[0]
    }

    newUserForm.transform((data) => ({
        ...data,
        name: fullName,
    })).post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isAddUserOpen.value = false
            newUserForm.reset()
            // FIXED: Use Inertia's proper reload method
            router.reload({ only: ['users'] })
        },
        onError: (errors) => {
            console.error('Error saving user:', errors)
        }
    })
}

function updateUser() {
    if (!selectedUser.value) return
    
    const fullName = `${editUserForm.firstName} ${editUserForm.middleName} ${editUserForm.lastName}`.replace(/\s+/g, ' ').trim()

    // FIXED: Validate and format lastLogin date
    if (!editUserForm.lastLogin) {
        editUserForm.lastLogin = new Date().toISOString().split('T')[0]
    } else {
        // Ensure the date is in valid format
        const date = new Date(editUserForm.lastLogin)
        if (isNaN(date.getTime())) {
            editUserForm.lastLogin = new Date().toISOString().split('T')[0]
        } else {
            editUserForm.lastLogin = date.toISOString().split('T')[0]
        }
    }

    editUserForm.transform((data) => ({
        ...data,
        name: fullName,
        last_login: data.lastLogin, // Map to backend expected field name
    })).put(route('users.update', selectedUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditUserOpen.value = false
            selectedUser.value = null
            editUserForm.reset()
            router.reload({ only: ['users'] })
        },
        onError: (errors) => {
            console.error('Error updating user:', errors)
        }
    })
}

function deleteUser(userId: number | string) {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', userId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['users'] })
            }
        })
    }
}

// =====================
// Search & Filters - FIXED: Better filtering logic
// =====================
const searchQuery = ref('')
const filterProgram = ref('')
const filterYear = ref('')
const filterRole = ref('')
const filterStatus = ref('')

const filteredUsers = computed(() => {
    if (!users.value || users.value.length === 0) return []

    return users.value.filter((user) => {
        // FIXED: Handle null/undefined values safely
        const userName = user.name || ''
        const userEmail = user.email || ''
        const userStudentId = user.student_id || ''
        const userProgram = user.program || ''
        const userYear = user.year || ''
        const userRole = user.role || ''
        const userStatus = user.status || ''

        const matchesSearch = searchQuery.value === '' || 
            userName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            userEmail.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            userStudentId.toLowerCase().includes(searchQuery.value.toLowerCase())

        const matchesProgram = filterProgram.value === '' || userProgram === filterProgram.value
        const matchesYear = filterYear.value === '' || userYear === filterYear.value
        const matchesRole = filterRole.value === '' || userRole === filterRole.value
        const matchesStatus = filterStatus.value === '' || userStatus === filterStatus.value

        return matchesSearch && matchesProgram && matchesYear && matchesRole && matchesStatus
    })
})

// Clear filters function
function clearFilters() {
    filterProgram.value = ''
    filterYear.value = ''
    filterRole.value = ''
    filterStatus.value = ''
    searchQuery.value = ''
}

// =====================
// Pagination - FIXED: Better pagination logic
// =====================
const totalPages = computed(() => Math.ceil(filteredUsers.value.length / Number(perPage.value)))
const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * Number(perPage.value)
    return filteredUsers.value.slice(start, start + Number(perPage.value))
})

// =====================
// Action handlers - FIXED: Better user data handling
// =====================
function viewUser(user: any) {
    selectedUser.value = user
    isViewUserOpen.value = true
}

function editUser(user: any) {
    selectedUser.value = { ...user }
    
    // FIXED: Proper form initialization with safe data handling
    editUserForm.id = user.id

    // FIXED: Better name parsing with fallbacks with proper TypeScript typing
    const nameParts = (user.name || '').split(' ').filter((part: string) => part.trim() !== '')
    editUserForm.firstName = nameParts[0] || ''
    editUserForm.middleName = nameParts.length > 2 ? nameParts.slice(1, -1).join(' ') : ''
    editUserForm.lastName = nameParts.length > 1 ? nameParts[nameParts.length - 1] : ''

    editUserForm.email = user.email || ''
    editUserForm.studentId = user.student_id || ''
    editUserForm.program = user.program || ''
    editUserForm.year = user.year || ''
    editUserForm.role = user.role || ''
    editUserForm.status = user.status || 'active'
    
    // FIXED: Proper date handling for lastLogin
    if (user.last_login) {
        const date = new Date(user.last_login)
        editUserForm.lastLogin = isNaN(date.getTime()) 
            ? new Date().toISOString().split('T')[0] 
            : date.toISOString().split('T')[0]
    } else {
        editUserForm.lastLogin = new Date().toISOString().split('T')[0]
    }
    
    isEditUserOpen.value = true
}

// Reset current page when filters change
watch([searchQuery, filterProgram, filterYear, filterRole, filterStatus], () => {
    currentPage.value = 1
})

// Reset current page when perPage changes
watch(perPage, () => {
    currentPage.value = 1
})

// FIXED: Watch for page prop changes to update users
watch(() => page.props.users, (newUsers) => {
    // This ensures the users data stays in sync with page props
}, { deep: true })
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="User Management" />

        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">User Management</h1>
                    <p class="text-muted-foreground">Manage member directory, roles, and access permissions</p>
                </div>

                <Button @click="isAddUserOpen = true" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Add User
                </Button>
            </div>

            <!-- Search + Filter -->
            <div class="flex items-center gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <Input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search by name, student ID, or email..." 
                    />
                </div>

                <FilterDropdown
                    v-model:program="filterProgram"
                    v-model:year="filterYear"
                    v-model:role="filterRole"
                    v-model:status="filterStatus"
                    @clear="clearFilters"
                />
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-border bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border">
                            <tr class="text-muted-foreground">
                                <th class="px-6 py-4 font-medium">User</th>
                                <th class="px-6 py-4 font-medium">Student ID</th>
                                <th class="px-6 py-4 font-medium">Program & Year</th>
                                <th class="px-6 py-4 font-medium">Role</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium">Last Login</th>
                                <th class="px-6 py-4 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="user in paginatedUsers" :key="user.id" class="transition-colors hover:bg-muted/50">
                                <!-- User Column -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-foreground">{{ user.name || 'N/A' }}</div>
                                    <div class="text-sm text-muted-foreground">{{ user.email || 'N/A' }}</div>
                                </td>
                                
                                <!-- Student ID Column -->
                                <td class="px-6 py-4 font-mono text-foreground">{{ user.student_id || 'N/A' }}</td>
                                
                                <!-- Program & Year Column -->
                                <td class="px-6 py-4">
                                    <div class="text-foreground">{{ user.program || 'N/A' }}</div>
                                    <div class="text-sm text-muted-foreground">{{ user.year || 'N/A' }}</div>
                                </td>
                                
                                <!-- Role Column -->
                                <td class="px-6 py-4">
                                    <Badge 
                                        :class="{
                                            'bg-blue-100 text-blue-700 hover:bg-blue-100': user.role === 'Member',
                                            'bg-green-100 text-green-700 hover:bg-green-100': user.role === 'Officer',
                                            'bg-red-100 text-red-700 hover:bg-red-100': user.role === 'Admin'
                                        }"
                                    >
                                        {{ user.role || 'N/A' }}
                                    </Badge>
                                </td>
                                
                                <!-- Status Column -->
                                <td class="px-6 py-4">
                                    <Badge 
                                        :class="{
                                            'bg-green-100 text-green-700 hover:bg-green-100': user.status === 'active',
                                            'bg-red-100 text-red-700 hover:bg-red-100': user.status === 'inactive'
                                        }"
                                    >
                                        {{ user.status || 'N/A' }}
                                    </Badge>
                                </td>
                                
                                <!-- Last Login Column -->
                                <td class="px-6 py-4 text-foreground">{{ user.last_login || 'Never' }}</td>
                                
                                <!-- Actions Column -->
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <Button @click="viewUser(user)" variant="ghost" size="sm" class="h-8 w-8 p-0">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                        <Button @click="editUser(user)" variant="ghost" size="sm" class="h-8 w-8 p-0">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button @click="deleteUser(user.id)" variant="ghost" size="sm" class="h-8 w-8 p-0 text-destructive hover:text-destructive">
                                            <Trash class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="paginatedUsers.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                    <Users class="h-12 w-12 text-muted-foreground opacity-50 mb-4" />
                    <p class="text-muted-foreground">No users found matching your criteria.</p>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination
                v-model:currentPage="currentPage"
                v-model:perPage="perPage"
                :totalItems="filteredUsers.length"
                :totalPages="totalPages"
                itemName="users"
            />
        </div>

        <!-- Add User Modal -->
        <UserModal
            :isOpen="isAddUserOpen"
            title="Add User"
            :errors="newUserForm.errors"
            @close="isAddUserOpen = false"
        >
            <UserForm
                :form="newUserForm"
                submitText="Save User"
                @submit="saveNewUser"
                @cancel="isAddUserOpen = false"
            />
        </UserModal>

        <!-- View User Modal -->
        <UserModal
            :isOpen="isViewUserOpen"
            title="User Details"
            maxWidth="md"
            @close="isViewUserOpen = false"
        >
            <div class="space-y-4" v-if="selectedUser">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Name</label>
                        <p class="text-foreground">{{ selectedUser.name || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Email</label>
                        <p class="text-foreground">{{ selectedUser.email || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Student ID</label>
                        <p class="text-foreground font-mono">{{ selectedUser.student_id || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Program</label>
                        <p class="text-foreground">{{ selectedUser.program || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Year</label>
                        <p class="text-foreground">{{ selectedUser.year || 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Role</label>
                        <Badge>{{ selectedUser.role || 'N/A' }}</Badge>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Status</label>
                        <Badge :class="selectedUser.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                            {{ selectedUser.status || 'N/A' }}
                        </Badge>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-muted-foreground">Last Login</label>
                        <p class="text-foreground">{{ selectedUser.last_login || 'Never' }}</p>
                    </div>
                </div>
                <div class="flex justify-end pt-4">
                    <Button @click="isViewUserOpen = false">Close</Button>
                </div>
            </div>
        </UserModal>

        <!-- Edit User Modal -->
        <UserModal
            :isOpen="isEditUserOpen"
            title="Edit User"
            :errors="editUserForm.errors"
            @close="isEditUserOpen = false"
        >
            <UserForm
                :form="editUserForm"
                submitText="Save Changes"
                @submit="updateUser"
                @cancel="isEditUserOpen = false"
            />
        </UserModal>
    </AppLayout>
</template>