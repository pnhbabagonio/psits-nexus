<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ChevronDown, Pencil, Plus, Trash } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

// =====================
// Search & Filters
// =====================

const isAddUserOpen = ref(false);
const isViewUserOpen = ref(false);
const isEditUserOpen = ref(false);
const isFilterOpen = ref(false); // for dropdown toggle
const selectedUser = ref<any>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    // NAVBAR TITLE
    {
        title: 'User Management',
        href: '/user-management',
    },
];

// Pagination state
const currentPage = ref(1);
const perPage = ref(10);

// Use data from Laravel backend
const page = usePage();
const users = ref(Array.isArray(page.props.users) ? page.props.users : []);

// Add User Form
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
    lastLogin: new Date().toISOString().split('T')[0],
});

// Edit User Form
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
});

function saveNewUser() {
    const fullName = `${newUserForm.firstName} ${newUserForm.middleName} ${newUserForm.lastName}`.replace(/\s+/g, ' ').trim();

    newUserForm
        .transform((data) => ({
            ...data,
            name: fullName,
        }))
        .post(route('users.store'), {
            preserveScroll: true,
            onSuccess: () => {
                isAddUserOpen.value = false;
                newUserForm.reset();
                router.reload({ only: ['users'] });
            },
        });
}

function updateUser() {
    if (!selectedUser.value) return;
    const fullName = `${editUserForm.firstName} ${editUserForm.middleName} ${editUserForm.lastName}`.replace(/\s+/g, ' ').trim();

    editUserForm
        .transform((data) => ({
            ...data,
            name: fullName,
        }))
        .put(route('users.update', selectedUser.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                isEditUserOpen.value = false;
                selectedUser.value = null;
                router.reload({ only: ['users'] });
            },
        });
}

function deleteUser(userId: number | string) {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', userId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['users'] });
            },
        });
    }
}

// =====================
// Search & Filters
// =====================
// Filter dropdown ref
// Filter dropdown ref — type it as the actual element
const filterDropdown = ref<HTMLDivElement | null>(null);

// Handle clicks outside the filter dropdown
function handleClickOutside(event: MouseEvent) {
  const el = filterDropdown.value;
  // event.target is EventTarget | null, so narrow it to Node before using .contains
  if (el && event.target instanceof Node && !el.contains(event.target)) {
    isFilterOpen.value = false;
  }
}

// Add event listener when component mounts
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

// Remove event listener when component unmounts
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const searchQuery = ref('');
const filters = ref({
    program: '',
    year: '',
    role: '',
    status: '',
});

const filteredUsers = computed(() => {
    return users.value.filter((user) => {
        const matchesSearch =
            user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            user.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (user.student_id || '').toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesProgram = filters.value.program ? user.program === filters.value.program : true;
        const matchesYear = filters.value.year ? user.year === filters.value.year : true;
        const matchesRole = filters.value.role ? user.role === filters.value.role : true;
        const matchesStatus = filters.value.status ? user.status === filters.value.status : true;

        return matchesSearch && matchesProgram && matchesYear && matchesRole && matchesStatus;
    });
});

// =====================
// Pagination (based on filtered users)
// =====================
const totalPages = computed(() => Math.ceil(filteredUsers.value.length / perPage.value));
const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return filteredUsers.value.slice(start, start + perPage.value);
});

// =====================
// Action handlers
// =====================
function viewUser(user: any) {
    selectedUser.value = user;
    isViewUserOpen.value = true;
}

function editUser(user: any) {
    selectedUser.value = { ...user };
    editUserForm.id = user.id;

    const nameParts = user.name.split(' ');
    editUserForm.firstName = nameParts[0] || '';
    editUserForm.middleName = nameParts.length > 2 ? nameParts.slice(1, -1).join(' ') : '';
    editUserForm.lastName = nameParts.length > 1 ? nameParts[nameParts.length - 1] : '';

    editUserForm.email = user.email;
    editUserForm.studentId = user.student_id;
    editUserForm.program = user.program;
    editUserForm.year = user.year;
    editUserForm.role = user.role;
    editUserForm.status = user.status;
    editUserForm.lastLogin = user.last_login;
    isEditUserOpen.value = true;
}

watch(
    [searchQuery, filters],
    () => {
        currentPage.value = 1;
    },
    { deep: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="User Management" />

        <div class="space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">User Management</h1>
                    <p class="text-gray-400">Manage member directory, roles, and access permissions</p>
                </div>

<<<<<<< HEAD
    <!-- Search + Filter (Dropdown) -->
    <div class="flex items-center gap-4 mb-4">
      <!-- Search Input -->
      <div class="flex-1">
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Search by name, student ID, or email..." 
          class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2"
        />
      </div>

    <div class="relative" ref="filterDropdown">
    <button 
      @click="isFilterOpen = !isFilterOpen"
      class="flex items-center gap-2 px-3 py-2 bg-gray-700 rounded hover:bg-gray-600"
    >
      <span>Filter</span>
      <ChevronDown 
        class="w-4 h-4 transition-transform" 
        :class="{ 'rotate-180': isFilterOpen }" 
      />
    </button>

    <!-- Dropdown content -->
    <div 
      v-if="isFilterOpen" 
      class="absolute right-0 mt-2 w-64 bg-gray-900 border border-gray-700 rounded-lg shadow-lg p-4 z-[9999] space-y-3"
    >
      <select v-model="filters.program" class="w-full bg-gray-800 text-white border border-gray-700 rounded px-2 py-1 text-sm">
        <option value="">All Programs</option>
        <option value="Library and Information Science">Library and Information Science</option>
        <option value="Computer Science">Computer Science</option>
        <option value="Information System">Information System</option>
      </select>

      <select v-model="filters.year" class="w-full bg-gray-800 text-white border border-gray-700 rounded px-2 py-1 text-sm">
        <option value="">All Years</option>
        <option value="1st Year">1st Year</option>
        <option value="2nd Year">2nd Year</option>
        <option value="3rd Year">3rd Year</option>
        <option value="4th Year">4th Year</option>
        <option value="5th Year">5th Year</option>
      </select>

      <select v-model="filters.role" class="w-full bg-gray-800 text-white border border-gray-700 rounded px-2 py-1 text-sm">
        <option value="">All Roles</option>
        <option value="Member">Member</option>
        <option value="Officer">Officer</option>
      </select>

      <select v-model="filters.status" class="w-full bg-gray-800 text-white border border-gray-700 rounded px-2 py-1 text-sm">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>

      <button 
        @click="
          filters.program = '';
          filters.year = '';
          filters.role = '';
          filters.status = '';
          searchQuery = '';
        "
        class="w-full px-3 py-1 bg-gray-700 text-sm text-white rounded hover:bg-gray-600 mt-2"
      >
        Clear Filters
      </button>
    </div>
  </div>

</div>

    <!-- Add User Modal -->
    <div v-if="isAddUserOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-slate-900 p-6 rounded-lg w-full max-w-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Add User</h2>

        <!-- form -->
        <form @submit.prevent="saveNewUser" class="space-y-4">
        <!-- ERROR DISPLAY -->
        <div v-if="newUserForm.errors && Object.keys(newUserForm.errors).length" class="p-3 bg-red-900 text-red-200 rounded">
            <p class="font-semibold">Please fix the following errors:</p>
            <ul class="list-disc list-inside mt-1">
            <li v-for="(error, field) in newUserForm.errors" :key="field">{{ error }}</li>
            </ul>
        </div>
        
        <!-- Name -->
        <div>
            <label class="block text-sm font-medium">First Name</label>
            <input v-model="newUserForm.firstName" type="text" class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2" required />
            <div v-if="newUserForm.errors.firstName" class="text-red-500 text-sm mt-1">{{ newUserForm.errors.firstName }}</div>

            <label class="block text-sm font-medium">Middle Name</label>
            <input v-model="newUserForm.middleName" type="text" class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2" />
            <div v-if="newUserForm.errors.middleName" class="text-red-500 text-sm mt-1">{{ newUserForm.errors.middleName }}</div>

            <label class="block text-sm font-medium">Last Name</label>
            <input v-model="newUserForm.lastName" type="text" class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2" required />
            <div v-if="newUserForm.errors.lastName" class="text-red-500 text-sm mt-1">{{ newUserForm.errors.lastName }}</div>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input v-model="newUserForm.email" type="email" class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2" required />
            <div v-if="newUserForm.errors.email" class="text-red-500 text-sm mt-1">{{ newUserForm.errors.email }}</div>
        </div>

        <!-- Student ID -->
        <div>
            <label class="block text-sm font-medium mb-1">Student ID</label>
            <input 
            v-model="newUserForm.studentId" 
            type="text" 
            class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2"
            />
            <div v-if="newUserForm.errors.studentId" class="text-red-500 text-sm mt-1">{{ newUserForm.errors.studentId }}</div>
        </div>

        <!-- Program + Year -->
        <div class="flex gap-4">
            <div class="w-1/2">
            <label class="block text-sm font-medium mb-1">Program</label>
            <select 
                v-model="newUserForm.program" 
                class="w-full bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2"
            >
                <option value="Library and Information Science">Library and Information Science</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Information System">Information System</option>
            </select>
            <div v-if="newUserForm.errors.program" class="text-red-500 text-sm mt-1">{{ newUserForm.errors.program }}</div>
=======
                <button
                    @click="isAddUserOpen = true"
                    class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700"
                >
                    <Plus class="h-4 w-4" />
                    Add User
                </button>
>>>>>>> 7bb47dc75cab79cde749d428a0b41c4f39c52f08
            </div>

            <!-- Search + Filter (Dropdown) -->
            <div class="mb-4 flex items-center gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, student ID, or email..."
                        class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                    />
                </div>

                <div class="relative" ref="filterDropdown">
                    <button @click="isFilterOpen = !isFilterOpen" class="flex items-center gap-2 rounded bg-gray-700 px-3 py-2 hover:bg-gray-600">
                        <span>Filter</span>
                        <ChevronDown class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isFilterOpen }" />
                    </button>

                    <!-- Dropdown content -->
                    <div
                        v-if="isFilterOpen"
                        class="absolute right-0 z-[9999] mt-2 w-64 space-y-3 rounded-lg border border-gray-700 bg-gray-900 p-4 shadow-lg"
                    >
                        <select v-model="filters.program" class="w-full rounded border border-gray-700 bg-gray-800 px-2 py-1 text-sm text-white">
                            <option value="">All Programs</option>
                            <option value="Library and Information Science">Library and Information Science</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Information System">Information System</option>
                        </select>

                        <select v-model="filters.year" class="w-full rounded border border-gray-700 bg-gray-800 px-2 py-1 text-sm text-white">
                            <option value="">All Years</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                            <option value="5th Year">5th Year</option>
                        </select>

                        <select v-model="filters.role" class="w-full rounded border border-gray-700 bg-gray-800 px-2 py-1 text-sm text-white">
                            <option value="">All Roles</option>
                            <option value="Member">Member</option>
                            <option value="Officer">Officer</option>
                        </select>

                        <select v-model="filters.status" class="w-full rounded border border-gray-700 bg-gray-800 px-2 py-1 text-sm text-white">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <button
                            @click="
                                filters.program = '';
                                filters.year = '';
                                filters.role = '';
                                filters.status = '';
                                searchQuery = '';
                            "
                            class="mt-2 w-full rounded bg-gray-700 px-3 py-1 text-sm text-white hover:bg-gray-600"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add User Modal -->
            <div v-if="isAddUserOpen" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
                <div class="w-full max-w-lg rounded-lg bg-slate-900 p-6 shadow-lg">
                    <h2 class="mb-4 text-xl font-semibold">Add User</h2>

                    <!-- form -->
                    <form @submit.prevent="saveNewUser" class="space-y-4">
                        <!-- ERROR DISPLAY -->
                        <div v-if="newUserForm.errors && Object.keys(newUserForm.errors).length" class="rounded bg-red-900 p-3 text-red-200">
                            <p class="font-semibold">Please fix the following errors:</p>
                            <ul class="mt-1 list-inside list-disc">
                                <li v-for="(error, field) in newUserForm.errors" :key="field">{{ error }}</li>
                            </ul>
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium">First Name</label>
                            <input
                                v-model="newUserForm.firstName"
                                type="text"
                                class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                                required
                            />
                            <div v-if="newUserForm.errors.firstName" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.firstName }}</div>

                            <label class="block text-sm font-medium">Middle Name</label>
                            <input
                                v-model="newUserForm.middleName"
                                type="text"
                                class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                            />
                            <div v-if="newUserForm.errors.middleName" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.middleName }}</div>

                            <label class="block text-sm font-medium">Last Name</label>
                            <input
                                v-model="newUserForm.lastName"
                                type="text"
                                class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                                required
                            />
                            <div v-if="newUserForm.errors.lastName" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.lastName }}</div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium">Email</label>
                            <input
                                v-model="newUserForm.email"
                                type="email"
                                class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                                required
                            />
                            <div v-if="newUserForm.errors.email" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.email }}</div>
                        </div>

                        <!-- Student ID -->
                        <div>
                            <label class="mb-1 block text-sm font-medium">Student ID</label>
                            <input
                                v-model="newUserForm.studentId"
                                type="text"
                                class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                            />
                            <div v-if="newUserForm.errors.studentId" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.studentId }}</div>
                        </div>

                        <!-- Program + Year -->
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <label class="mb-1 block text-sm font-medium">Program</label>
                                <select
                                    v-model="newUserForm.program"
                                    class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                                >
                                    <option value="Library and Information Science">Library and Information Science</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Information System">Information System</option>
                                </select>
                                <div v-if="newUserForm.errors.program" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.program }}</div>
                            </div>
                            <div class="w-1/2">
                                <label class="mb-1 block text-sm font-medium">Year Level</label>
                                <select v-model="newUserForm.year" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200">
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                    <option value="5th Year">5th Year</option>
                                </select>
                                <div v-if="newUserForm.errors.year" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.year }}</div>
                            </div>
                        </div>

                        <!-- Role + Status -->
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <label class="mb-1 block text-sm font-medium">Role</label>
                                <select v-model="newUserForm.role" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200">
                                    <option value="Member">Member</option>
                                    <option value="Officer">Officer</option>
                                </select>
                                <div v-if="newUserForm.errors.role" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.role }}</div>
                            </div>
                            <div class="w-1/2">
                                <label class="mb-1 block text-sm font-medium">Status</label>
                                <select
                                    v-model="newUserForm.status"
                                    class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div v-if="newUserForm.errors.status" class="mt-1 text-sm text-red-500">{{ newUserForm.errors.status }}</div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="isAddUserOpen = false" class="rounded bg-gray-700 px-4 py-2 hover:bg-gray-600">
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="newUserForm.processing"
                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                            >
                                {{ newUserForm.processing ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6 p-6">
            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-700 shadow">
                <table class="w-full text-left text-gray-300">
                    <thead class="bg-gray-900 text-sm text-gray-400 uppercase">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Student ID</th>
                            <th class="px-4 py-3">Program & Year</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last Login</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-800">
                            <!-- User Column -->
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ user.name }}</div>
                                <div class="text-sm text-gray-400">{{ user.email }}</div>
                            </td>

                            <!-- Student ID Column -->
                            <td class="px-4 py-3 font-mono">{{ user.student_id }}</td>

                            <!-- Program & Year Column -->
                            <td class="px-4 py-3">
                                <div>{{ user.program }}</div>
                                <div class="text-sm text-gray-400">{{ user.year }}</div>
                            </td>

                            <!-- Role Column -->
                            <td class="px-4 py-3">
                                <span
                                    :class="{
                                        'rounded-full px-2 py-1 text-xs': true,
                                        'bg-blue-500/20 text-blue-300': user.role === 'Member',
                                        'bg-green-500/20 text-green-300': user.role === 'Officer',
                                    }"
                                >
                                    {{ user.role }}
                                </span>
                            </td>

                            <!-- Status Column -->
                            <td class="px-4 py-3">
                                <span
                                    :class="{
                                        'rounded-full px-2 py-1 text-xs': true,
                                        'bg-green-500/20 text-green-300': user.status === 'active',
                                        'bg-red-500/20 text-red-300': user.status === 'inactive',
                                    }"
                                >
                                    {{ user.status }}
                                </span>
                            </td>

                            <!-- Last Login Column -->
                            <td class="px-4 py-3">{{ user.last_login }}</td>

                            <!-- Actions Column -->
                            <td class="flex gap-3 px-4 py-3">
                                <!-- View -->
                                <button @click="viewUser(user)" class="hover:text-blue-400" title="View">
                                    <svg
                                        class="h-5 w-5 cursor-pointer text-gray-400 hover:text-blue-500"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12s-4 7.5-10.5 7.5S1.5 12 1.5 12z"
                                        />
                                        <circle cx="12" cy="12" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <!-- Edit -->
                                <button @click="editUser(user)" class="hover:text-yellow-400" title="Edit">
                                    <Pencil class="h-5 w-5 text-gray-400 hover:text-yellow-500" />
                                </button>
                                <!-- Delete -->
                                <button @click="deleteUser(user.id)" class="hover:text-red-400" title="Delete">
                                    <Trash class="h-5 w-5 text-gray-400 hover:text-red-500" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex items-center justify-between p-6 text-sm text-gray-400">
            <!-- Left: Info text -->
            <p>
                Showing
                {{ (currentPage - 1) * perPage + 1 }}
                –
                {{ Math.min(currentPage * perPage, filteredUsers.length) }}
                of {{ filteredUsers.length }} users (page {{ currentPage }} of {{ totalPages }})
            </p>

            <!-- Right: Rows per page + Pagination -->
            <div class="flex items-center gap-6">
                <!-- Rows per page -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">Rows per page:</span>
                    <select v-model="perPage" class="rounded border border-gray-700 bg-gray-800 px-2 py-1 text-sm" @change="currentPage = 1">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <!-- Pagination buttons -->
                <div class="flex items-center gap-2">
                    <!-- Previous -->
                    <button
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="rounded bg-gray-700 px-3 py-1 hover:bg-gray-600 disabled:opacity-50"
                    >
                        Previous
                    </button>

                    <!-- Page numbers -->
                    <div v-for="page in totalPages" :key="page">
                        <button
                            v-if="Math.abs(page - currentPage) <= 2 || page === 1 || page === totalPages"
                            @click="currentPage = page"
                            :class="['rounded px-3 py-1', currentPage === page ? 'bg-blue-600 text-white' : 'bg-gray-700 hover:bg-gray-600']"
                        >
                            {{ page }}
                        </button>
                        <span v-else-if="Math.abs(page - currentPage) === 3" class="px-2 py-1 text-gray-500"> ... </span>
                    </div>

                    <!-- Next -->
                    <button
                        @click="currentPage = Math.min(totalPages, currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="rounded bg-gray-700 px-3 py-1 hover:bg-gray-600 disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- View User Modal -->
        <div v-if="isViewUserOpen" class="bg-opacity-50 fixed inset-0 flex items-center justify-center bg-black">
            <div class="w-[400px] rounded-lg bg-gray-900 p-6">
                <h2 class="mb-4 text-xl font-bold">User Details</h2>
                <p><strong>Name:</strong> {{ selectedUser.name }}</p>
                <p><strong>Email:</strong> {{ selectedUser.email }}</p>
                <p><strong>Student ID:</strong> {{ selectedUser.student_id }}</p>
                <p><strong>Program:</strong> {{ selectedUser.program }}</p>
                <p><strong>Year:</strong> {{ selectedUser.year }}</p>
                <p><strong>Role:</strong> {{ selectedUser.role }}</p>
                <p><strong>Status:</strong> {{ selectedUser.status }}</p>
                <p><strong>Last Login:</strong> {{ selectedUser.last_login }}</p>
                <div class="mt-4 flex justify-end">
                    <button @click="isViewUserOpen = false" class="rounded bg-gray-700 px-3 py-2">Close</button>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div v-if="isEditUserOpen" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
            <div class="w-full max-w-lg rounded-lg bg-slate-900 p-6 shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">Edit User</h2>

                <!-- Error summary -->
                <div v-if="editUserForm.errors && Object.keys(editUserForm.errors).length" class="mb-4 rounded bg-red-900 p-3 text-red-200">
                    <p class="font-semibold">Please fix the following errors:</p>
                    <ul class="mt-1 list-inside list-disc">
                        <li v-for="(error, field) in editUserForm.errors" :key="field">{{ error }}</li>
                    </ul>
                </div>

                <form @submit.prevent="updateUser" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium">First Name</label>
                        <input
                            v-model="editUserForm.firstName"
                            type="text"
                            class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                        />
                        <div v-if="editUserForm.errors.firstName" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.firstName }}</div>

                        <label class="block text-sm font-medium">Middle Name</label>
                        <input
                            v-model="editUserForm.middleName"
                            type="text"
                            class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                        />
                        <div v-if="editUserForm.errors.middleName" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.middleName }}</div>

                        <label class="block text-sm font-medium">Last Name</label>
                        <input
                            v-model="editUserForm.lastName"
                            type="text"
                            class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                        />
                        <div v-if="editUserForm.errors.lastName" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.lastName }}</div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input
                            v-model="editUserForm.email"
                            type="email"
                            class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                            required
                        />
                        <div v-if="editUserForm.errors.email" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.email }}</div>
                    </div>

                    <!-- Student ID -->
                    <div>
                        <label class="block text-sm font-medium">Student ID</label>
                        <input
                            v-model="editUserForm.studentId"
                            type="text"
                            class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200"
                        />
                        <div v-if="editUserForm.errors.studentId" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.studentId }}</div>
                    </div>

                    <!-- Program + Year -->
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium">Program</label>
                            <select v-model="editUserForm.program" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200">
                                <option value="Library and Information Science">Library and Information Science</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Information System">Information System</option>
                            </select>
                            <div v-if="editUserForm.errors.program" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.program }}</div>
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium">Year Level</label>
                            <select v-model="editUserForm.year" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200">
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                            <div v-if="editUserForm.errors.year" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.year }}</div>
                        </div>
                    </div>

                    <!-- Role + Status -->
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium">Role</label>
                            <select v-model="editUserForm.role" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200">
                                <option value="Member">Member</option>
                                <option value="Officer">Officer</option>
                            </select>
                            <div v-if="editUserForm.errors.role" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.role }}</div>
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium">Status</label>
                            <select v-model="editUserForm.status" class="w-full rounded border border-gray-700 bg-gray-800 px-3 py-2 text-gray-200">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div v-if="editUserForm.errors.status" class="mt-1 text-sm text-red-500">{{ editUserForm.errors.status }}</div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" @click="isEditUserOpen = false" class="rounded bg-gray-700 px-4 py-2 hover:bg-gray-600">Cancel</button>

                        <button
                            type="submit"
                            :disabled="editUserForm.processing"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ editUserForm.processing ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
