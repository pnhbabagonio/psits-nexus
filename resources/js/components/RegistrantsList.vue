<!-- resources/js/components/RegistrantsList.vue -->
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Users, Download, UserMinus } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import RegistrationModal from '@/components/RegistrationModal.vue'

interface Registrant {
    id: number
    user_id: number
    name: string
    email: string
    program: string
    student_id: string
    registered_at: string
}

interface Props {
    event: {
        id: number
        title: string
        description?: string
        date?: string
        time?: string
        venue?: string
        address?: string
        status?: string
        registered?: string
        max_capacity?: number
        organizer?: string
        user_id?: number
    }
    open: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
    close: []
    updated: []
}>()

const isLoading = ref(false)
const registrants = ref<Registrant[]>([])
const waitlisted = ref<Registrant[]>([])
const stats = ref({
    registered_count: 0,
    waitlisted_count: 0,
    max_capacity: 0,
    available_spots: 0
})

// Get CSRF token safely
function getCsrfToken(): string {
    const meta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null
    return meta?.content || ''
}

// Load registrants data
async function loadRegistrants() {
    isLoading.value = true
    try {
        const response = await fetch(`/events/${props.event.id}/registrants`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'same-origin'
        })

        if (!response.ok) throw new Error('Failed to load registrants')

        const data = await response.json()
        registrants.value = data.registrants
        waitlisted.value = data.waitlisted
        stats.value = data.stats
    } catch (error) {
        console.error('Error loading registrants:', error)
        alert('Failed to load registrants data')
    } finally {
        isLoading.value = false
    }
}

// Remove registrant
async function removeRegistrant(registrationId: number, userName: string) {
    if (!confirm(`Are you sure you want to remove ${userName} from this event?`)) return

    try {
        const response = await fetch(`/events/${props.event.id}/registrants/${registrationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })

        if (!response.ok) throw new Error('Failed to remove registrant')

        // Reload data
        await loadRegistrants()
        emit('updated')
    } catch (error) {
        console.error('Error removing registrant:', error)
        alert('Failed to remove registrant')
    }
}

// Export to CSV
function exportToCSV() {
    const allRegistrants = [...registrants.value, ...waitlisted.value]
    const headers = ['Name', 'Email', 'Program', 'Student ID', 'Status', 'Registered At']
    const csvData = allRegistrants.map(reg => [
        reg.name,
        reg.email,
        reg.program || 'N/A',
        reg.student_id || 'N/A',
        registrants.value.find(r => r.id === reg.id) ? 'Registered' : 'Waitlisted',
        reg.registered_at
    ])

    const csvContent = [
        headers.join(','),
        ...csvData.map(row => row.map(field => `"${field}"`).join(','))
    ].join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `registrants-${props.event.title.replace(/[^a-z0-9]/gi, '-').toLowerCase()}.csv`
    a.click()
    window.URL.revokeObjectURL(url)
}

// Load data when modal opens
onMounted(() => {
    if (props.open) {
        loadRegistrants()
    }
})
</script>

<template>
    <RegistrationModal :open="open" :title="`Registrants for ${event.title}`"
        description="Manage event registrations and waitlist" @close="emit('close')">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <Card>
                <CardContent class="p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ stats.registered_count }}</div>
                    <div class="text-sm text-muted-foreground">Registered</div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ stats.available_spots }}</div>
                    <div class="text-sm text-muted-foreground">Available</div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-4 text-center">
                    <div class="text-2xl font-bold text-amber-600">{{ stats.waitlisted_count }}</div>
                    <div class="text-sm text-muted-foreground">Waitlisted</div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-4 text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ stats.max_capacity }}</div>
                    <div class="text-sm text-muted-foreground">Capacity</div>
                </CardContent>
            </Card>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-6 max-h-96 overflow-y-auto">
            <!-- Registered Users -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Badge variant="default" class="bg-blue-500">Registered</Badge>
                        <span class="text-sm text-muted-foreground">
                            ({{ registrants.length }} users)
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-0">
                    <div v-if="registrants.length === 0" class="text-center py-4 text-muted-foreground text-sm">
                        No registered users yet
                    </div>
                    <div v-else class="space-y-2">
                        <div v-for="registrant in registrants" :key="registrant.id"
                            class="flex items-center justify-between p-2 border rounded-lg">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate">{{ registrant.name }}</div>
                                <div class="text-xs text-muted-foreground truncate">{{ registrant.email }}</div>
                                <div class="text-xs text-muted-foreground mt-1">
                                    {{ registrant.program }} • {{ registrant.student_id }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    Registered: {{ registrant.registered_at }}
                                </div>
                            </div>
                            <Button variant="outline" size="sm"
                                @click="removeRegistrant(registrant.id, registrant.name)"
                                class="flex items-center gap-1 text-red-600 hover:text-red-700 hover:bg-red-50 ml-2">
                                <UserMinus class="h-3 w-3" />
                                Remove
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Waitlisted Users -->
            <Card v-if="waitlisted.length > 0">
                <CardHeader class="pb-3">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Badge variant="outline" class="text-amber-600 border-amber-600">Waitlisted</Badge>
                        <span class="text-sm text-muted-foreground">
                            ({{ waitlisted.length }} users)
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-0">
                    <div class="space-y-2">
                        <div v-for="(waitlist, index) in waitlisted" :key="waitlist.id"
                            class="flex items-center justify-between p-2 border rounded-lg">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm truncate">{{ waitlist.name }}</div>
                                <div class="text-xs text-muted-foreground truncate">{{ waitlist.email }}</div>
                                <div class="text-xs text-muted-foreground mt-1">
                                    {{ waitlist.program }} • {{ waitlist.student_id }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    Waitlisted: {{ waitlist.registered_at }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-2">
                                <span class="text-xs text-muted-foreground whitespace-nowrap">#{{ index + 1 }}</span>
                                <Button variant="outline" size="sm"
                                    @click="removeRegistrant(waitlist.id, waitlist.name)"
                                    class="flex items-center gap-1 text-red-600 hover:text-red-700 hover:bg-red-50">
                                    <UserMinus class="h-3 w-3" />
                                    Remove
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <template #footer>
            <Button variant="outline" @click="exportToCSV" class="flex items-center gap-2">
                <Download class="h-4 w-4" />
                Export CSV
            </Button>
            <Button @click="emit('close')">
                Close
            </Button>
        </template>
    </RegistrationModal>
</template>