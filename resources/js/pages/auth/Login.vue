<!-- Login.vue -->
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { Eye, EyeOff, LoaderCircle, Lock, Mail, AlertCircle, CheckCircle } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Props {
    status?: string;
    canResetPassword: boolean;
    errors?: Record<string, string>;
}

const props = withDefaults(defineProps<Props>(), {
    status: '',
    canResetPassword: true,
    errors: () => ({})
});

const showPassword = ref(false);
const rememberMe = ref(false);

// Use Inertia's useForm for better form handling
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const handleSubmit = () => {
    form.remember = rememberMe.value;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
        onError: (errors) => {
            // Handle specific error cases
            if (errors.email || errors.password) {
                // Focus on the first field with an error
                if (errors.email) {
                    document.getElementById('email')?.focus();
                } else if (errors.password) {
                    document.getElementById('password')?.focus();
                }
            }
        }
    });
};

// Computed property to check if form has any errors
const hasErrors = computed(() => {
    return Object.keys(form.errors).length > 0;
});

// Status message type detection
const statusType = computed(() => {
    if (!props.status) return '';
    
    // Common success messages
    const successKeywords = ['success', 'sent', 'verified', 'completed', 'updated'];
    const warningKeywords = ['pending', 'waiting', 'review'];
    
    const status = props.status.toLowerCase();
    
    if (successKeywords.some(keyword => status.includes(keyword))) {
        return 'success';
    }
    if (warningKeywords.some(keyword => status.includes(keyword))) {
        return 'warning';
    }
    return 'info';
});
</script>

<template>
    <AuthBase 
        title="Welcome back" 
        description="Sign in to your account to continue your journey"
    >
        <Head title="Log in" />

        <!-- Status Message -->
        <div 
            v-if="status" 
            class="mb-6 rounded-lg border p-4 transition-all duration-200"
            :class="{
                'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20': statusType === 'success',
                'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20': statusType === 'warning',
                'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20': statusType === 'info'
            }"
        >
            <div class="flex items-center">
                <CheckCircle 
                    v-if="statusType === 'success'" 
                    class="mr-3 h-5 w-5 text-green-500" 
                />
                <AlertCircle 
                    v-else-if="statusType === 'warning'" 
                    class="mr-3 h-5 w-5 text-amber-500" 
                />
                <svg 
                    v-else
                    class="mr-3 h-5 w-5 text-blue-500" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <p 
                    class="text-sm font-medium"
                    :class="{
                        'text-green-800 dark:text-green-300': statusType === 'success',
                        'text-amber-800 dark:text-amber-300': statusType === 'warning',
                        'text-blue-800 dark:text-blue-300': statusType === 'info'
                    }"
                >
                    {{ status }}
                </p>
            </div>
        </div>

        <!-- Error Summary -->
        <div 
            v-if="hasErrors" 
            class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20"
        >
            <div class="flex items-start">
                <AlertCircle class="mr-3 mt-0.5 h-5 w-5 text-red-500" />
                <div>
                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-300">
                        Please correct the following errors:
                    </h4>
                    <ul class="mt-1 text-xs text-red-700 dark:text-red-400">
                        <li v-for="(error, key) in form.errors" :key="key">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="space-y-5">
                <!-- Email Field -->
                <div class="space-y-2">
                    <Label 
                        for="email" 
                        class="text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        Email address
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
                            name="email"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="Enter your email address"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.email,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.errors.email
                            }"
                            class="h-12 bg-gray-50 pl-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700 dark:focus:border-blue-400"
                        />
                    </div>
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label 
                            for="password" 
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300"
                        >
                            Password
                            <span class="text-red-500">*</span>
                        </Label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-xs font-medium text-blue-600 transition-colors duration-200 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                        >
                            Forgot password?
                        </Link>
                    </div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            :disabled="form.processing"
                            :class="{
                                'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.password,
                                'border-gray-200 focus:border-blue-500 focus:ring-blue-500': !form.errors.password
                            }"
                            class="h-12 bg-gray-50 pl-12 pr-12 transition-all duration-200 focus:bg-white disabled:opacity-50 dark:bg-gray-700 dark:focus:border-blue-400"
                        />
                        <button
                            type="button"
                            @click="togglePasswordVisibility"
                            :disabled="form.processing"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 transition-colors duration-200 hover:text-gray-600 focus:outline-none focus:text-gray-600 disabled:opacity-50"
                            :title="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <Eye v-if="!showPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <InputError :message="form.errors.password" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <Label 
                        for="remember" 
                        class="flex cursor-pointer items-center space-x-3 group"
                    >
                        <Checkbox 
                            id="remember" 
                            v-model="rememberMe"
                            :disabled="form.processing"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 disabled:opacity-50" 
                        />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors">
                            Keep me signed in
                        </span>
                    </Label>
                </div>

                <!-- Submit Button -->
                <Button
                    type="submit"
                    :disabled="form.processing || !form.email || !form.password"
                    class="h-12 w-full transform rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-[1.02] hover:from-blue-700 hover:to-blue-800 hover:shadow-xl focus:scale-[1.02] focus:from-blue-700 focus:to-blue-800 focus:shadow-xl disabled:transform-none disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                    <Lock v-else class="mr-2 h-5 w-5" />
                    {{ form.processing ? 'Signing in...' : 'Sign in to your account' }}
                </Button>
            </div>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-4 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        or
                    </span>
                </div>
            </div>

            <!-- Sign Up Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <Link 
                        :href="route('register')" 
                        class="ml-1 font-semibold text-blue-600 transition-colors duration-200 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                    >
                        Create a new account
                    </Link>
                </p>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <div class="flex items-start">
                    <svg 
                        class="mr-3 mt-0.5 h-5 w-5 text-blue-500 flex-shrink-0" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300">
                            Secure Connection
                        </h4>
                        <p class="mt-1 text-xs text-blue-700 dark:text-blue-400">
                            Your login credentials are protected with industry-standard encryption and security protocols.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </AuthBase>
</template>

<style scoped>
/* Custom focus styles for better accessibility */
.focus\:ring-blue-500:focus {
    --tw-ring-opacity: 0.3;
}

/* Smooth transitions for all interactive elements */
input, button, a {
    transition-property: all;
    transition-duration: 200ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>