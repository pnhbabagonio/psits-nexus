<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from "@inertiajs/vue3"
import { Calendar, MapPin, Clock, Users, FileText, Edit, Trash2 } from "lucide-vue-next"

// shadcn-vue components
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

interface Event {
    id: number
    title: string
    time: string
    date: string
    venue: string
    address: string
    description: string
    registered: string
    max_capacity: number
    status: "Upcoming" | "Ongoing" | "Completed"
    organizer: string
}

const props = defineProps<{
    event: Event
    isOpen: boolean
}>()

const emit = defineEmits<{
    close: []
    edit: [event: Event]
    delete: [eventId: number]
}>()

const showDeleteDialog = ref(false)

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

// Calculate registration percentage
const registrationPercentage = computed(() => {
    const registered = parseInt(props.event.registered) || 0
    const maxCapacity = props.event.max_capacity || 1
    return Math.round((registered / maxCapacity) * 100)
})
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

                <!-- Registration Stats Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Registration
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium">Progress</span>
                                <span class="text-sm text-muted-foreground">
                                    {{ event.registered }} / {{ event.max_capacity }} ({{ registrationPercentage }}%)
                                </span>
                            </div>
                            <div class="w-full bg-secondary rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full transition-all duration-300" :class="{
                                    'bg-blue-500': event.status === 'Upcoming',
                                    'bg-emerald-500': event.status === 'Ongoing',
                                    'bg-gray-500': event.status === 'Completed'
                                }" :style="{ width: `${Math.min(registrationPercentage, 100)}%` }"></div>
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
</template>