<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const emit = defineEmits<{
    userCreated: []
}>()

// Form state
const form = reactive({
    name: '',
    email: '',
    studentId: '',
    program: '',
    year: '',
    role: 'Member' as 'Member' | 'Officer' | 'Admin',
    status: 'active' as 'active' | 'inactive',
    lastLogin: ''
})

const isSubmitting = ref(false)
const errors = ref<Record<string, string>>({})

// Form validation
const validateForm = () => {
    errors.value = {}
    
    if (!form.name.trim()) {
        errors.value.name = 'Name is required'
    }
    
    if (!form.email.trim()) {
        errors.value.email = 'Email is required'
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.value.email = 'Please enter a valid email address'
    }
    
    if (!form.program.trim()) {
        errors.value.program = 'Program is required'
    }
    
    if (!form.year.trim()) {
        errors.value.year = 'Year is required'
    }
    
    return Object.keys(errors.value).length === 0
}

// Submit form
const submitForm = () => {
    if (!validateForm()) return
    
    isSubmitting.value = true
    
    router.post('/users', form, {
        onSuccess: () => {
            resetForm()
            emit('userCreated')
        },
        onError: (serverErrors) => {
            errors.value = serverErrors
        },
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}

// Reset form
const resetForm = () => {
    Object.assign(form, {
        name: '',
        email: '',
        studentId: '',
        program: '',
        year: '',
        role: 'Member',
        status: 'active',
        lastLogin: ''
    })
    errors.value = {}
}

// Programs list (you can customize this based on your needs)
const programs = [
    'Computer Science',
    'Information Technology',
    'Software Engineering',
    'Data Science',
    'Cybersecurity',
    'Business Administration',
    'Accounting',
    'Marketing',
    'Engineering',
    'Other'
]

// Year levels
const years = [
    '1st Year',
    '2nd Year', 
    '3rd Year',
    '4th Year',
    '5th Year',
    'Graduate',
    'Alumni'
]
</script>

<template>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-white">Add New User</h2>
            <p class="text-gray-400 mt-1">Create a new user account for the system</p>
        </div>

        <!-- Form Card -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Personal Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Full Name <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full bg-gray-700 border rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition-colors"
                                :class="errors.name ? 'border-red-500 focus:ring-red-500' : 'border-gray-600 focus:ring-blue-500'"
                                placeholder="Enter full name"
                            />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-400">{{ errors.name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Email Address <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full bg-gray-700 border rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition-colors"
                                :class="errors.email ? 'border-red-500 focus:ring-red-500' : 'border-gray-600 focus:ring-blue-500'"
                                placeholder="Enter email address"
                            />
                            <p v-if="errors.email" class="mt-1 text-sm text-red-400">{{ errors.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Academic Information
                    </h3>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <!-- Student ID -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Student ID
                            </label>
                            <input
                                v-model="form.studentId"
                                type="text"
                                class="w-full bg-gray-700 border rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition-colors"
                                :class="errors.studentId ? 'border-red-500 focus:ring-red-500' : 'border-gray-600 focus:ring-blue-500'"
                                placeholder="Enter student ID"
                            />
                            <p v-if="errors.studentId" class="mt-1 text-sm text-red-400">{{ errors.studentId }}</p>
                        </div>

                        <!-- Program -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Program <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="form.program"
                                class="w-full bg-gray-700 border rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 transition-colors"
                                :class="errors.program ? 'border-red-500 focus:ring-red-500' : 'border-gray-600 focus:ring-blue-500'"
                            >
                                <option value="">Select Program</option>
                                <option v-for="program in programs" :key="program" :value="program">
                                    {{ program }}
                                </option>
                            </select>
                            <p v-if="errors.program" class="mt-1 text-sm text-red-400">{{ errors.program }}</p>
                        </div>

                        <!-- Year -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Year Level <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="form.year"
                                class="w-full bg-gray-700 border rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 transition-colors"
                                :class="errors.year ? 'border-red-500 focus:ring-red-500' : 'border-gray-600 focus:ring-blue-500'"
                            >
                                <option value="">Select Year</option>
                                <option v-for="year in years" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                            <p v-if="errors.year" class="mt-1 text-sm text-red-400">{{ errors.year }}</p>
                        </div>
                    </div>
                </div>

                <!-- System Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        System Information
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Role <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="form.role"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                            >
                                <option value="Member">Member</option>
                                <option value="Officer">Officer</option>
                                <option value="Admin">Admin</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-400">
                                Default role is Member. Admins have full access.
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Status <span class="text-red-400">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-400">
                                Active users can login to the system.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Default Password Notice -->
                <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-300 mb-1">Default Password</h4>
                            <p class="text-sm text-yellow-200">
                                New users will be created with the default password <strong>"password123"</strong>. 
                                They should change this upon first login.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-700">
                    <button
                        type="button"
                        @click="resetForm"
                        class="px-6 py-2 text-gray-300 hover:text-white transition-colors"
                        :disabled="isSubmitting"
                    >
                        Reset Form
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-800 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors"
                        :disabled="isSubmitting"
                    >
                        <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ isSubmitting ? 'Creating User...' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>