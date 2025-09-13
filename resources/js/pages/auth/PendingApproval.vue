<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Clock, ArrowLeft, Mail, Shield } from 'lucide-vue-next';

const props = defineProps<{
    email?: string;
}>();

const goToWelcome = () => {
    window.location.href = '/';
};
</script>

<template>
    <AuthBase title="Account Pending Approval" description="Your account is awaiting administrator confirmation">
        <Head title="Pending Approval" />

        <Card class="w-full max-w-md border-blue-200 bg-blue-50/50 dark:border-blue-800 dark:bg-blue-900/20">
            <CardHeader class="text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                    <Clock class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                </div>
                <CardTitle class="text-2xl font-bold text-blue-900 dark:text-blue-100">Account Under Review</CardTitle>
                <CardDescription class="mt-2 text-blue-700 dark:text-blue-300">
                    Your registration is being processed
                </CardDescription>
            </CardHeader>

            <CardContent class="space-y-6">
                <!-- Status Message -->
                <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <div class="flex items-start">
                        <Shield class="mr-3 h-5 w-5 text-blue-500 mt-0.5" />
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">What's happening?</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Your account registration has been received and is currently awaiting administrator approval. 
                                This process typically takes 24-48 hours.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Email Confirmation -->
                <div v-if="email" class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                    <div class="flex items-start">
                        <Mail class="mr-3 h-5 w-5 text-green-500 mt-0.5" />
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Confirmation Sent</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                A confirmation has been sent to <span class="font-medium text-blue-600 dark:text-blue-400">{{ email }}</span>. 
                                Please check your inbox (and spam folder) for updates.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="rounded-lg bg-amber-50 p-4 dark:bg-amber-900/20">
                    <h4 class="font-semibold text-amber-900 dark:text-amber-100">What to expect next:</h4>
                    <ul class="mt-2 space-y-1 text-sm text-amber-800 dark:text-amber-200">
                        <li>• Administrator review of your registration details</li>
                        <li>• Email notification once your account is approved</li>
                        <li>• Access to all system features upon approval</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <Button 
                        @click="goToWelcome" 
                        class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
                    >
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Return to Welcome Page
                    </Button>
                    
                    <Button 
                        variant="outline" 
                        as="a" 
                        :href="route('login')" 
                        class="w-full border-blue-300 text-blue-700 hover:bg-blue-50 dark:border-blue-600 dark:text-blue-300 dark:hover:bg-blue-900/30"
                    >
                        Already approved? Sign in here
                    </Button>
                </div>

                <!-- Support Contact -->
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Need help? Contact support at 
                        <a href="mailto:support@example.com" class="text-blue-600 hover:underline dark:text-blue-400">
                            support@example.com
                        </a>
                    </p>
                </div>
            </CardContent>
        </Card>
    </AuthBase>
</template>

<style scoped>
/* Smooth transitions for better user experience */
button {
    transition: all 0.2s ease-in-out;
}

button:hover {
    transform: translateY(-1px);
}
</style>