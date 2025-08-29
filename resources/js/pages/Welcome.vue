<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const features = ref([
    {
        title: 'Instant QR Payments',
        description: 'Generate and scan QR codes for seamless transactions with real-time processing.',
        icon: 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-4.01M12 12v4m6-4h-2M6 12h2m0-6V4m0 0h2m-2 0H4',
        color: 'text-blue-500',
    },
    {
        title: 'Financial Insights',
        description: 'Comprehensive analytics dashboard for tracking all financial activities.',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        color: 'text-emerald-500',
    },
    {
        title: 'Smart Automation',
        description: 'Automate payments, receipts, and financial reporting to save time.',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
        color: 'text-purple-500',
    },
    {
        title: 'Member Engagement',
        description: 'Track participation and engagement with detailed member analytics.',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        color: 'text-rose-500',
    },
]);

const loaded = ref(false);
const activeFeature = ref(0);

onMounted(() => {
    setTimeout(() => {
        loaded.value = true;
    }, 100);

    // Rotate active feature every 3 seconds
    setInterval(() => {
        activeFeature.value = (activeFeature.value + 1) % features.value.length;
    }, 3000);
});
</script>

<template>
    <Head title="PSITS Nexus - Digital Payment Platform">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <meta
            name="description"
            content="Modern digital payment platform for organizations with QR payments, financial automation, and engagement analytics."
        />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 text-gray-900 transition-colors duration-500 dark:from-gray-900 dark:to-blue-900/20 dark:text-gray-100"
        :class="{ 'opacity-100': loaded, 'opacity-0': !loaded }"
    >
        <!-- Floating background elements -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 h-64 w-64 rounded-full bg-blue-200/20 blur-3xl dark:bg-blue-800/20"></div>
            <div class="absolute right-20 bottom-20 h-64 w-64 rounded-full bg-purple-200/20 blur-3xl dark:bg-purple-800/20"></div>
        </div>

        <!-- Navigation -->
        <nav class="container mx-auto flex items-center justify-between px-6 py-6">
            <div class="flex items-center space-x-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-blue-700">
                    <span class="font-bold text-white">P</span>
                </div>
                <span class="text-xl font-semibold">PSITS Nexus</span>
            </div>
            <div class="flex items-center space-x-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="rounded-lg bg-blue-600 px-5 py-2 font-medium text-white transition-colors duration-200 hover:bg-blue-700"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="rounded-lg px-5 py-2 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                        Sign In
                    </Link>
                    <Link
                        :href="route('register')"
                        class="rounded-lg bg-blue-600 px-5 py-2 font-medium text-white transition-colors duration-200 hover:bg-blue-700"
                    >
                        Get Started
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="container mx-auto px-6 py-12 md:py-24">
            <div class="flex flex-col items-center gap-12 lg:flex-row">
                <!-- Content -->
                <div class="space-y-8 lg:w-1/2">
                    <h1 class="text-4xl leading-tight font-bold md:text-5xl">
                        Modern Payment Platform for
                        <span class="relative inline-block">
                            <span class="relative z-10">Student Organizations</span>
                            <span class="absolute bottom-2 left-0 h-3 w-full -rotate-1 bg-blue-200/50 dark:bg-blue-800/50"></span>
                        </span>
                    </h1>
                    <p class="max-w-lg text-lg text-gray-600 dark:text-gray-300">
                        PSITS Nexus provides cutting-edge digital payment solutions with QR technology, financial automation, and comprehensive
                        analytics for student organizations.
                    </p>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('register')"
                            class="flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 font-medium text-white shadow-lg transition-colors duration-200 hover:bg-blue-700 hover:shadow-xl"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            Get Started Free
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 font-medium text-white shadow-lg transition-colors duration-200 hover:bg-blue-700 hover:shadow-xl"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            Go to Dashboard
                        </Link>
                        <a
                            href="#features"
                            class="flex items-center justify-center rounded-lg border border-gray-200 px-6 py-3 font-medium transition-colors duration-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Visual -->
                <div class="relative lg:w-1/2">
                    <div class="relative z-10 mx-auto w-full max-w-md">
                        <div
                            class="aspect-square overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-800"
                        >
                            <!-- Phone mockup with rotating feature screens -->
                            <div class="relative h-full w-full overflow-hidden">
                                <div
                                    v-for="(feature, index) in features"
                                    :key="index"
                                    class="absolute inset-0 flex flex-col items-center justify-center p-8 transition-opacity duration-500"
                                    :class="{ 'opacity-100': activeFeature === index, 'opacity-0': activeFeature !== index }"
                                >
                                    <div
                                        class="mb-6 flex h-16 w-16 items-center justify-center rounded-xl"
                                        :class="feature.color.replace('text', 'bg') + '/10'"
                                    >
                                        <svg class="h-8 w-8" :class="feature.color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                                        </svg>
                                    </div>
                                    <h3 class="mb-2 text-center text-xl font-bold">{{ feature.title }}</h3>
                                    <p class="text-center text-gray-500 dark:text-gray-400">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating QR code -->
                        <div
                            class="animate-float absolute -right-6 -bottom-6 h-32 w-32 rounded-xl border border-gray-100 bg-white p-3 shadow-xl dark:border-gray-700 dark:bg-gray-800"
                        >
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-600 to-blue-700">
                                <div class="grid h-20 w-20 grid-cols-3 gap-1">
                                    <div
                                        v-for="i in 9"
                                        :key="i"
                                        class="rounded-sm"
                                        :class="Math.random() > 0.3 ? 'bg-white' : 'bg-transparent'"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="container mx-auto px-6 py-16 md:py-24">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold md:text-4xl">Powerful Features</h2>
                <p class="mx-auto max-w-2xl text-gray-600 dark:text-gray-300">
                    Everything your organization needs to manage payments, finances, and member engagement in one platform.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(feature, index) in features"
                    :key="index"
                    class="rounded-xl border border-gray-100 bg-white p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-lg" :class="feature.color.replace('text', 'bg') + '/10'">
                        <svg class="h-6 w-6" :class="feature.color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                        </svg>
                    </div>
                    <h3 class="mb-3 text-xl font-bold">{{ feature.title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ feature.description }}</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-blue-600 to-blue-700 py-16 text-white md:py-24">
            <div class="container mx-auto px-6 text-center">
                <h2 class="mb-6 text-3xl font-bold md:text-4xl">Ready to transform your organization?</h2>
                <p class="mx-auto mb-8 max-w-2xl text-blue-100">
                    Join hundreds of student organizations using PSITS Nexus to streamline their payment processes and financial management.
                </p>
                <Link
                    :href="route($page.props.auth.user ? 'dashboard' : 'register')"
                    class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 font-bold text-blue-600 shadow-lg transition-colors duration-200 hover:bg-gray-100 hover:shadow-xl"
                >
                    {{ $page.props.auth.user ? 'Go to Dashboard' : 'Get Started Free' }}
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-50 py-12 dark:bg-gray-900/50">
            <div class="container mx-auto px-6">
                <div class="flex flex-col items-center justify-between md:flex-row">
                    <div class="mb-6 flex items-center space-x-3 md:mb-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-blue-700">
                            <span class="text-xs font-bold text-white">P</span>
                        </div>
                        <span class="font-semibold">PSITS Nexus</span>
                    </div>
                    <div class="flex space-x-6">
                        <a
                            href="#"
                            class="text-gray-600 transition-colors duration-200 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                            >Terms</a
                        >
                        <a
                            href="#"
                            class="text-gray-600 transition-colors duration-200 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                            >Privacy</a
                        >
                        <a
                            href="#"
                            class="text-gray-600 transition-colors duration-200 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                            >Contact</a
                        >
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-200 pt-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    © 2023 PSITS Nexus. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}
.animate-float {
    animation: float 6s ease-in-out infinite;
}
</style>
