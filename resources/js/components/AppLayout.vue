<!-- SIDEBAR AND NAV-->

<script setup lang="ts">
import AppSidebar from '@/Components/AppSidebar.vue';
import AppSidebarHeader from '@/Components/AppSidebarHeader.vue';

defineProps<{
<<<<<<< HEAD
  breadcrumbs?: Array<{ title: string; href: string }>
}>()
=======
    breadcrumbs?: Array<{ label: string; href: string }>;
}>();
>>>>>>> 7bb47dc75cab79cde749d428a0b41c4f39c52f08
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <AppSidebar />

        <!-- Main content -->
        <div class="flex flex-1 flex-col">
            <!-- Header -->
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />

            <!-- Page content -->
            <main class="flex-1 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
