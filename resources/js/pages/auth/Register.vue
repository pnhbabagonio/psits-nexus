<!-- Register.vue -->
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AuthBase from '@/layouts/AuthLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { 
    LoaderCircle, 
    Eye, 
    EyeOff, 
    User, 
    Mail, 
    Lock, 
    GraduationCap, 
    Phone, 
    Building,
    AlertCircle,
    CheckCircle,
    Info
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

interface Role {
    role_id: number;
    role_name: string;
}

interface Props {
    roles: Role[];
}

const props = defineProps<Props>();

const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const acceptedTerms = ref(false);
const currentStep = ref(1);
const totalSteps = 3;

// Form data using Inertia's useForm
const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    student_id: '',
    department: '',
    year_level: '',
    contact_number: '',
    terms: false,
});

// Available departments and year levels
const departments = [
    'Computer Science',
    'Information Technology',
    'Engineering',
    'Business Administration',
    'Education',
    'Liberal Arts',
    'Science',
    'Mathematics',
    'Psychology',
    'Other'
];

const yearLevels = [
    '1st Year',
    '2nd Year', 
    '3rd Year',
    '4th Year',
    '5th Year',
    'Graduate'
];

// Toggle password visibility
const togglePasswordVisibility = (field: 'password' | 'confirm') => {
    if (field === 'password') {
        showPassword.value = !showPassword.value;
    } else {
        showPasswordConfirm.value = !showPasswordConfirm.value;
    }
};

// Password strength checker
const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return { score: 0, text: '', color: '' };

    let score = 0;
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /\d/.test(password),
        special: /[@$!%*?&]/.test(password)
    };

    score = Object.values(checks).filter(Boolean).length;

    if (score < 3) return { score, text: 'Weak', color: 'text-red-500' };
    if (score < 5) return { score, text: 'Medium', color: 'text-yellow-500' };
    return { score, text: 'Strong', color: 'text-green-500' };
});

// Check if current step is valid
const isStepValid = computed(() => {
    switch (currentStep.value) {
        case 1:
            return form.first_name && form.last_name && form.email && !form.errors.first_name && !form.errors.last_name && !form.errors.email;
        case 2:
            return form.password && form.password_confirmation && passwordStrength.value.score >= 3 && form.password === form.password_confirmation;
        case 3:
            return form.role_id && acceptedTerms.value;
        default:
            return false;
    }
});

// Navigate between steps
const nextStep = () => {
    if (currentStep.value < totalSteps && isStepValid.value) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

// Handle form submission
const handleSubmit = () => {
    form.terms = acceptedTerms.value;
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
        onError: (errors) => {
            // Navigate to the step with errors
            if (errors.first_name || errors.last_name || errors.email) {
                currentStep.value = 1;
            } else if (errors.password || errors.password_confirmation) {
                currentStep.value = 2;
            } else {
                currentStep.value = 3;
            }
        }
    });
};

// Watch for role changes to show/hide student fields
const isStudentRole = computed(() => {
    const selectedRole = props.roles.find(role => role.role_id.toString() === form.role_id);
    return selectedRole?.role_name.toLowerCase().includes('student');
});

// Auto-format student ID
watch(() => form.student_id, (newValue) => {
    if (newValue) {
        // Remove any existing formatting
        let cleaned = newValue.replace(/\D/g, '');
        
        // Format as YYYY-XXXXXX
        if (cleaned.length >= 4) {
            cleaned = cleaned.slice(0, 4) + '-' + cleaned.slice(4, 10);
        }
        
        if (cleaned !== newValue) {
            form.student_id = cleaned;
        }
    }
});
</script>

<template>
    <AuthBase 
        title="Create your account" 
        description="Join our community and start your journey with us"
    >
        <Head title="Register" />

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Step {{ currentStep }} of {{ totalSteps }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ Math.round((currentStep / totalSteps) * 100) }}% Complete
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                <div 
                    class="bg-blue-600 h-2 rounded-full transition-all duration-300 ease-out"
                    :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                />
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Step 1: Personal Information -->
            <div v-if="currentStep === 1" class="space-y-5">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Personal Information
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Let's start with your basic information
                    </p>
                </div>

                <!-- First Name -->
                <div class="space-y-2">
                    <Label for="first_name" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        First Name
                        <span class="text-red-500">*</span>
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <User class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            required
                            autofocus
                            autocomplete="given-name"
                            placeholder="Enter your first name"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.first_name,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.errors.first_name
                            }"
                            class="h-12 bg-gray-50 pl-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700"
                        />
                    </div>
                    <InputError :message="form.errors.first_name" />
                </div>

                <!-- Last Name -->
                <div class="space-y-2">
                    <Label for="last_name" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Last Name
                        <span class="text-red-500">*</span>
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <User class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            required
                            autocomplete="family-name"
                            placeholder="Enter your last name"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.last_name,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.errors.last_name
                            }"
                            class="h-12 bg-gray-50 pl-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700"
                        />
                    </div>
                    <InputError :message="form.errors.last_name" />
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Email Address
                        <span class="text-red-500">*</span>
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Mail class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="Enter your email address"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.email,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.errors.email
                            }"
                            class="h-12 bg-gray-50 pl-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700"
                        />
                    </div>
                    <InputError :message="form.errors.email" />
                </div>
            </div>

            <!-- Step 2: Security -->
            <div v-else-if="currentStep === 2" class="space-y-5">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Account Security
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Create a strong password to secure your account
                    </p>
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <Label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Password
                        <span class="text-red-500">*</span>
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="new-password"
                            placeholder="Create a strong password"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.errors.password
                            }"
                            class="h-12 bg-gray-50 pl-12 pr-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700"
                        />
                        <button
                            type="button"
                            @click="togglePasswordVisibility('password')"
                            :disabled="form.processing"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none disabled:opacity-50"
                        >
                            <Eye v-if="!showPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </button>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div v-if="form.password" class="mt-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400">Password strength:</span>
                            <span :class="passwordStrength.color" class="font-medium">
                                {{ passwordStrength.text }}
                            </span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 rounded-full h-1 dark:bg-gray-700">
                            <div 
                                class="h-1 rounded-full transition-all duration-300"
                                :class="{
                                    'bg-red-500': passwordStrength.score < 3,
                                    'bg-yellow-500': passwordStrength.score >= 3 && passwordStrength.score < 5,
                                    'bg-green-500': passwordStrength.score === 5
                                }"
                                :style="{ width: `${(passwordStrength.score / 5) * 100}%` }"
                            />
                        </div>
                    </div>
                    
                    <InputError :message="form.errors.password" />
                    
                    <!-- Password Requirements -->
                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <p class="font-medium">Password must contain:</p>
                        <ul class="space-y-1 ml-4">
                            <li :class="form.password.length >= 8 ? 'text-green-600' : 'text-gray-400'">
                                • At least 8 characters
                            </li>
                            <li :class="/[a-z]/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                • One lowercase letter
                            </li>
                            <li :class="/[A-Z]/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                • One uppercase letter
                            </li>
                            <li :class="/\d/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                • One number
                            </li>
                            <li :class="/[@$!%*?&]/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                • One special character (@$!%*?&)
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <Label for="password_confirmation" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Confirm Password
                        <span class="text-red-500">*</span>
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            :type="showPasswordConfirm ? 'text' : 'password'"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm your password"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password_confirmation || (form.password_confirmation && form.password !== form.password_confirmation),
                                'border-green-300 focus:border-green-500 focus:ring-green-500': form.password_confirmation && form.password === form.password_confirmation,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.password_confirmation
                            }"
                            class="h-12 bg-gray-50 pl-12 pr-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700"
                        />
                        <button
                            type="button"
                            @click="togglePasswordVisibility('confirm')"
                            :disabled="form.processing"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none disabled:opacity-50"
                        >
                            <Eye v-if="!showPasswordConfirm" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </button>
                    </div>
                    
                    <!-- Password Match Indicator -->
                    <div v-if="form.password_confirmation" class="flex items-center mt-2 text-xs">
                        <CheckCircle v-if="form.password === form.password_confirmation" class="h-4 w-4 text-green-500 mr-2" />
                        <AlertCircle v-else class="h-4 w-4 text-red-500 mr-2" />
                        <span :class="form.password === form.password_confirmation ? 'text-green-600' : 'text-red-600'">
                            {{ form.password === form.password_confirmation ? 'Passwords match' : 'Passwords do not match' }}
                        </span>
                    </div>
                    
                    <InputError :message="form.errors.password_confirmation" />
                </div>
            </div>

            <!-- Step 3: Role & Additional Info -->
            <div v-else-if="currentStep === 3" class="space-y-5">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Account Details
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Complete your profile information
                    </p>
                </div>

                <!-- Role Selection -->
                <div class="space-y-2">
                    <Label for="role_id" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Role
                        <span class="text-red-500">*</span>
                    </Label>
                    <Select v-model="form.role_id" :disabled="form.processing">
                        <SelectTrigger class="h-12 bg-gray-50 focus:bg-white dark:bg-gray-700">
                            <SelectValue placeholder="Select your role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem 
                                v-for="role in roles" 
                                :key="role.role_id" 
                                :value="role.role_id.toString()"
                            >
                                {{ role.role_name.charAt(0).toUpperCase() + role.role_name.slice(1) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role_id" />
                </div>

                <!-- Student-specific fields -->
                <div v-if="isStudentRole" class="space-y-5 border-t pt-5 dark:border-gray-600">
                    <div class="flex items-center mb-3">
                        <GraduationCap class="h-5 w-5 text-blue-600 mr-2" />
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Student Information</h4>
                    </div>

                    <!-- Student ID -->
                    <div class="space-y-2">
                        <Label for="student_id" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Student ID
                        </Label>
                        <Input
                            id="student_id"
                            v-model="form.student_id"
                            type="text"
                            placeholder="2024-123456"
                            :disabled="form.processing"
                            maxlength="11"
                            class="h-12 bg-gray-50 focus:bg-white dark:bg-gray-700"
                        />
                        <p class="text-xs text-gray-500">Format: YYYY-XXXXXX (e.g., 2024-123456)</p>
                        <InputError :message="form.errors.student_id" />
                    </div>

                    <!-- Department -->
                    <div class="space-y-2">
                        <Label for="department" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Department
                        </Label>
                        <Select v-model="form.department" :disabled="form.processing">
                            <SelectTrigger class="h-12 bg-gray-50 focus:bg-white dark:bg-gray-700">
                                <SelectValue placeholder="Select your department" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="dept in departments" :key="dept" :value="dept">
                                    {{ dept }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.department" />
                    </div>

                    <!-- Year Level -->
                    <div class="space-y-2">
                        <Label for="year_level" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Year Level
                        </Label>
                        <Select v-model="form.year_level" :disabled="form.processing">
                            <SelectTrigger class="h-12 bg-gray-50 focus:bg-white dark:bg-gray-700">
                                <SelectValue placeholder="Select your year level" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="year in yearLevels" :key="year" :value="year">
                                    {{ year }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.year_level" />
                    </div>
                </div>

                <!-- Contact Number -->
                <div class="space-y-2">
                    <Label for="contact_number" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Contact Number
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Phone class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="contact_number"
                            v-model="form.contact_number"
                            type="tel"
                            placeholder="+63 9XX XXX XXXX"
                            :disabled="form.processing"
                            class="h-12 bg-gray-50 pl-12 focus:bg-white dark:bg-gray-700"
                        />
                    </div>
                    <InputError :message="form.errors.contact_number" />
                </div>

                <!-- Terms and Conditions -->
                <div class="space-y-3 border-t pt-5 dark:border-gray-600">
                    <Label class="flex items-start space-x-3 cursor-pointer group">
                        <Checkbox 
                            v-model="acceptedTerms"
                            :disabled="form.processing"
                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors">
                            I agree to the 
                            <Link href="#" class="text-blue-600 hover:text-blue-500 underline">Terms of Service</Link>
                            and 
                            <Link href="#" class="text-blue-600 hover:text-blue-500 underline">Privacy Policy</Link>
                        </span>