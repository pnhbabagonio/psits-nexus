<script setup lang="ts">
import { ref } from "vue"
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import Requirements from "@/components/Requirements.vue"
import Records from "@/components/Records.vue"
import Profiles from "@/components/Profiles.vue"

const activeTab = ref("requirements")

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Payment', href: '/payment' },
]

const tabs = [
    { key: "requirements", label: "Requirements" },
    { key: "records", label: "Payment Records" },
    { key: "profiles", label: "User Profiles" },
]
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 text-gray-200">
            <!-- Tabs Navigation -->
            <div class="flex space-x-6 border-b border-gray-800 mb-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    class="pb-2"
                    :class="[
                    activeTab === tab.key
                        ? 'border-b-2 border-blue-500 text-blue-400'
                        : 'text-gray-400 hover:text-gray-200'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Tab Content -->
            <div>
                <Requirements v-if="activeTab === 'requirements'" />
                <Records v-if="activeTab === 'records'" />
                <Profiles v-if="activeTab === 'profiles'" />
            </div>
        </div>
    </AppLayout>
</template>