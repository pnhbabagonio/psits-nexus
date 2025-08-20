<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-screen flex-col items-center justify-center px-6 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <!-- Enhanced Left Panel -->
        <div class="relative hidden h-full flex-col p-10 text-white lg:flex overflow-hidden">
            <!-- Sophisticated Background with Multiple Layers -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800">
                <!-- Animated Orb Effects -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-20 -left-20 w-96 h-96 bg-gradient-to-r from-blue-600/30 to-purple-600/30 rounded-full mix-blend-multiply filter blur-3xl animate-float"></div>
                    <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-gradient-to-r from-emerald-600/30 to-blue-600/30 rounded-full mix-blend-multiply filter blur-3xl animate-float-delayed"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-violet-600/20 to-pink-600/20 rounded-full mix-blend-multiply filter blur-2xl animate-pulse"></div>
                </div>

                <!-- Subtle Grid Pattern -->
                <div class="absolute inset-0 opacity-5">
                    <div class="h-full w-full" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
                </div>

                <!-- Noise Texture for Premium Feel -->
                <div class="absolute inset-0 opacity-10 bg-gradient-to-br from-transparent via-white/5 to-transparent"></div>
            </div>

            <!-- Logo Section with Enhanced Design -->
            <Link :href="route('home')" class="relative z-20 group flex items-center text-xl font-semibold transition-all duration-300 hover:scale-105">
                <div class="mr-3 w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center shadow-2xl shadow-blue-500/25 group-hover:shadow-blue-500/40 transition-all duration-300">
                    <AppLogoIcon class="w-7 h-7 fill-current text-white" />
                </div>
                <span class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                    {{ name }}
                </span>
            </Link>

            <!-- Main Content Area -->
            <div class="relative z-20 flex-1 flex flex-col justify-center max-w-lg">
                <!-- Hero Section -->
                <div class="space-y-6 mb-12">
                    <h2 class="text-4xl font-bold leading-tight tracking-tight">
                        <span class="bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent">
                            Welcome to PSITS Nexus
                        </span>
                    </h2>
                    <p class="text-lg text-blue-100/80 leading-relaxed font-light">
                        Experience an easier way to access payment records, monitor finances, and stay up to date on society events and announcements.
                    </p>
                </div>

                <!-- Feature Cards -->
                <div class="space-y-4 mb-12">
                    <div class="group flex items-center space-x-4 p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                            <!-- <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg> -->
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white group-hover:text-blue-200 transition-colors">Payment Records</h3>
                            <p class="text-sm text-blue-100/70">Fast and efficient way to track all your payments</p>
                        </div>
                    </div>

                    <div class="group flex items-center space-x-4 p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                            <!-- <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg> -->
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white group-hover:text-emerald-200 transition-colors">Financial Monitoring</h3>
                            <p class="text-sm text-blue-100/70">Easily manage and oversee your finances.</p>
                        </div>
                    </div>

                    <div class="group flex items-center space-x-4 p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg">
                            <!-- <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg> -->
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white group-hover:text-violet-200 transition-colors">Event Updates</h3>
                            <p class="text-sm text-blue-100/70">Stay informed with the latest ativities, programs, and announcements</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Quote Section -->
            <div v-if="quote" class="relative z-20 mt-auto">
                <div class="p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                    <blockquote class="space-y-3">
                        <svg class="w-6 h-6 text-blue-300 mb-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                        </svg>
                        <p class="text-lg text-white font-medium leading-relaxed italic">
                            &ldquo;{{ quote.message }}&rdquo;
                        </p>
                        <footer class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-violet-500 flex items-center justify-center">
                                <span class="text-xs font-bold text-white">{{ quote.author.charAt(0) }}</span>
                            </div>
                            <span class="text-sm text-blue-200 font-medium">{{ quote.author }}</span>
                        </footer>
                    </blockquote>
                </div>
            </div>
        </div>

        <!-- Enhanced Right Panel -->
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-8 sm:w-[400px]">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <Link :href="route('home')" class="inline-flex items-center text-xl font-semibold text-gray-900 dark:text-white">
                        <div class="mr-3 w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center shadow-lg">
                            <AppLogoIcon class="w-6 h-6 fill-current text-white" />
                        </div>
                        {{ name }}
                    </Link>
                </div>

                <!-- Form Header with Better Typography -->
                <div class="text-center space-y-3" v-if="title || description">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed" v-if="description">
                        {{ description }}
                    </p>
                </div>

                <!-- Form Container with Enhanced Styling -->
                <div class="bg-white dark:bg-gray-900/50 rounded-2xl p-8 shadow-2xl border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                    <slot />
                </div>

                <!-- Security Badge -->
                <div class="text-center">
                    <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Secured with 256-bit SSL encryption</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations for floating elements */
@keyframes float {
    0%, 100% {
        transform: translate(0px, 0px) rotate(0deg);
    }
    33% {
        transform: translate(15px, -15px) rotate(1deg);
    }
    66% {
        transform: translate(-10px, 10px) rotate(-1deg);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translate(0px, 0px) rotate(0deg);
    }
    33% {
        transform: translate(-15px, 15px) rotate(-1deg);
    }
    66% {
        transform: translate(20px, -10px) rotate(1deg);
    }
}

.animate-float {
    animation: float 20s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 25s ease-in-out infinite;
}

/* Enhanced responsive design */
@media (max-width: 1023px) {
    .min-h-screen {
        min-height: 100vh;
        min-height: 100dvh;
    }
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.5);
}
</style>