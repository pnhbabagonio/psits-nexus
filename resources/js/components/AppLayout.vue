<!-- SIDEBAR AND NAV-->

<script setup lang="ts">
import AppSidebar from '@/Components/AppSidebar.vue'
import AppSidebarHeader from '@/Components/AppSidebarHeader.vue'

defineProps<{
  breadcrumbs?: Array<{ label: string; href: string }>
}>()
</script>

<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <AppSidebar />
    
    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <AppSidebarHeader :breadcrumbs="breadcrumbs" />
      
      <!-- Page content -->
      <main class="flex-1 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>