<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <AuthBase title="Welcome back" description="Sign in to your PSITS Nexus account to continue">
        <Head title="Log in" />

        <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ status }}</p>
            </div>
        </div>

        <Form method="post" :action="route('login')" :reset-on-success="['password']" v-slot="{ errors, processing }" class="space-y-6">
            <div class="space-y-5">
                <!-- Email Field -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Email address
                    </Label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Mail class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="Enter your email"
                            class="pl-12 h-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:border-blue-400 transition-all duration-200"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Password
                        </Label>
                        <TextLink 
                            v-if="canResetPassword" 
                            :href="route('password.request')" 
                            class="text-xs text-blue-600 hover:text-blue-500 font-medium" 
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </div>
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="pl-12 pr-12 h-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:border-blue-400 transition-all duration-200"
                        />
                        <button
                            type="button"
                            @click="togglePasswordVisibility"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        >
                            <Eye v-if="!showPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                        <Checkbox 
                            id="remember" 
                            name="remember" 
                            :tabindex="3"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Remember me</span>
                    </Label>
                </div>

                <!-- Submit Button -->
                <Button 
                    type="submit" 
                    class="w-full h-12 bg-gradient-to-r from-[#2563eb] to-[#1d4ed8] hover:from-[#1d4ed8] hover:to-[#1e40af] text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                    :tabindex="4" 
                    :disabled="processing"
                >
                    <LoaderCircle v-if="processing" class="w-5 h-5 animate-spin mr-2" />
                    <Lock v-else class="w-5 h-5 mr-2" />
                    {{ processing ? 'Signing in...' : 'Sign in' }}
                </Button>
            </div>

            <!-- Sign Up Link -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white dark:bg-gray-800 text-gray-500">or</span>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <TextLink 
                        :href="route('register')" 
                        :tabindex="5"
                        class="font-semibold text-blue-600 hover:text-blue-500 ml-1"
                    >
                        Create account
                    </TextLink>
                </p>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300">Secure Login</h4>
                        <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">
                            Your connection is encrypted and secured with industry-standard protocols.
                        </p>
                    </div>
                </div>
            </div>
        </Form>
    </AuthBase>
</template>