<!-- resources/js/components/EventDetail.vue -->
<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from "@inertiajs/vue3"
import { Calendar, MapPin, Clock, Users, FileText, Edit, Trash2, Check } from "lucide-vue-next"

// shadcn-vue components
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import RegistrationButton from '@/components/RegistrationButton.vue'
import RegistrantsList from '@/components/RegistrantsList.vue'

interface AppEvent {
    id: number
    title: string
    time: string
    date: string
    venue: string
    address: string
    description: string
    registered: string // Old manual count - we'll transition from this
    registered_count?: number // New dynamic count from registrations
    max_capacity: number
    status: "Upcoming" | "Ongoing" | "Completed"
    organizer: string
    user_id: number
    created_at: string
    updated_at: string
    // New properties for registration system
    has_available_spots?: boolean
    is_user_registered?: boolean
    is_user_waitlisted?: boolean
}

const props = defineProps<{
    event: AppEvent
    isOpen: boolean
}>()

const emit = defineEmits<{
    close: []
    edit: [event: AppEvent]
    delete: [eventId: number]
    update: [event: AppEvent]
}>()

// Declare all reactive variables
const showDeleteDialog = ref(false)
const showRegistrantsList = ref(false)
const page = usePage()

// Computed properties
const isEventOwner = computed(() => props.event.user_id === page.props.auth.user.id)
const isUserRegistered = computed(() => props.event.is_user_registered || false)
const isUserWaitlisted = computed(() => props.event.is_user_waitlisted || false)

// Use new registered_count if available, fallback to old registered
const currentRegisteredCount = computed(() => {
    return props.event.registered_count !== undefined
        ? props.event.registered_count
        : parseInt(props.event.registered) || 0
})

// Calculate registration percentage
const registrationPercentage = computed(() => {
    const registered = currentRegisteredCount.value
    const maxCapacity = props.event.max_capacity || 1
    return Math.round((registered / maxCapacity) * 100)
})

// Enhanced event object for RegistrationButton
const registrationEvent = computed(() => ({
    id: props.event.id,
    title: props.event.title,
    registered_count: currentRegisteredCount.value,
    max_capacity: props.event.max_capacity,
    has_available_spots: props.event.has_available_spots !== undefined
        ? props.event.has_available_spots
        : currentRegisteredCount.value < props.event.max_capacity
}))

function handleEdit() {
    emit('edit', props.event)
}

function handleDelete() {
    emit('delete', props.event.id)
    showDeleteDialog.value = false
}

function getStatusVariant(status: string) {
    switch (status) {
        case 'Upcoming': return 'default'
        case 'Ongoing': return 'secondary'
        case 'Completed': return 'outline'
        default: return 'default'
    }
}

function getStatusColor(status: string) {
    switch (status) {
        case 'Upcoming': return 'text-blue-600 bg-blue-50 border-blue-200'
        case 'Ongoing': return 'text-emerald-600 bg-emerald-50 border-emerald-200'
        case 'Completed': return 'text-gray-600 bg-gray-50 border-gray-200'
        default: return 'text-gray-600 bg-gray-50 border-gray-200'
    }
}

function handleRegistrationUpdate() {
    emit('update', props.event)
}

function handleEventUpdated() {
    emit('update', props.event)
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="emit('close')">
        <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <div class="flex items-center justify-between">
                    <DialogTitle class="text-2xl font-bold tracking-tight">
                        {{ event.title }}
                    </DialogTitle>
                    <Badge :variant="getStatusVariant(event.status)" :class="getStatusColor(event.status)">
                        {{ event.status }}
                    </Badge>
                </div>
                <DialogDescription class="text-lg text-muted-foreground">
                    Organized by {{ event.organizer }}
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-6">
                <!-- Event Details Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Event Details
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Calendar class="h-4 w-4" />
                                    <span class="font-medium">Date:</span>
                                </div>
                                <span>{{ event.date }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Clock class="h-4 w-4" />
                                    <span class="font-medium">Time:</span>
                                </div>
                                <span>{{ event.time }}</span>
                            </div>
                            <div class="flex items-center gap-3 md:col-span-2">
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <MapPin class="h-4 w-4" />
                                    <span class="font-medium">Venue:</span>
                                </div>
                                <div>
                                    <p class="font-medium">{{ event.venue }}</p>
                                    <p class="text-sm text-muted-foreground">{{ event.address }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Registration Management Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Registration
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <!-- Registration Progress -->
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium">Registration Progress</span>
                                    <span class="text-sm text-muted-foreground">
                                        {{ currentRegisteredCount }} / {{ event.max_capacity }} ({{
                                            registrationPercentage }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-secondary rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-300" :class="{
                                        'bg-blue-500': event.status === 'Upcoming',
                                        'bg-emerald-500': event.status === 'Ongoing',
                                        'bg-gray-500': event.status === 'Completed'
                                    }" :style="{ width: `${Math.min(registrationPercentage, 100)}%` }"></div>
                                </div>
                            </div>

                            <!-- Registration Button -->
                            <RegistrationButton :event="registrationEvent" :is-user-registered="isUserRegistered"
                                :is-user-waitlisted="isUserWaitlisted" @updated="handleRegistrationUpdate" />

                            <!-- Admin Controls -->
                            <div v-if="isEventOwner" class="border-t pt-4 mt-4">
                                <Button variant="outline" @click="showRegistrantsList = true"
                                    class="w-full flex items-center gap-2">
                                    <Users class="h-4 w-4" />
                                    View Registrants ({{ currentRegisteredCount }})
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Description Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Description
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground leading-relaxed">
                            {{ event.description || "No description provided for this event." }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="flex justify-between items-center pt-6 border-t">
                <Button variant="outline" @click="emit('close')">
                    Close
                </Button>

                <div class="flex gap-2">
                    <Button variant="outline" @click="handleEdit" class="flex items-center gap-2">
                        <Edit class="h-4 w-4" />
                        Edit Event
                    </Button>

                    <AlertDialog v-model:open="showDeleteDialog">
                        <AlertDialogTrigger as-child>
                            <Button variant="destructive" class="flex items-center gap-2">
                                <Trash2 class="h-4 w-4" />
                                Delete Event
                            </Button>
                        </AlertDialogTrigger>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                <AlertDialogDescription>
                                    This action cannot be undone. This will permanently delete the event
                                    "{{ event.title }}" and remove all associated data.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Cancel</AlertDialogCancel>
                                <AlertDialogAction @click="handleDelete" variant="destructive">
                                    Delete
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </div>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Registrants List Modal -->
    <RegistrantsList v-if="event" :event="event" :open="showRegistrantsList" @close="showRegistrantsList = false"
        @updated="handleEventUpdated" />
</template>