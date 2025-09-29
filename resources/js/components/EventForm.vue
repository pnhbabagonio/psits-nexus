<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { MapPin, Calendar, Clock, Users, DollarSign, Shield } from 'lucide-vue-next'

// shadcn-vue components
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import { Card, CardContent } from '@/components/ui/card'
import { Alert, AlertDescription } from '@/components/ui/alert'

const props = defineProps<{
    isOpen: boolean
    isEditing?: boolean
    event?: any
}>()

const emit = defineEmits<{
    close: []
    created: [event: any]
    updated: [event: any]
}>()

const form = useForm({
    title: '',
    description: '',
    date: '',
    time: '',
    venue: '',
    address: '',
    max_capacity: '',
    organizer: '',
    status: 'Upcoming',
})

// Reset form when dialog opens/closes or when event changes
watch(() => props.isOpen, (isOpen) => {
    if (!isOpen) {
        form.reset()
    }
})

watch(() => props.event, (event) => {
    if (event) {
        form.defaults({
            title: event.title || '',
            description: event.description || '',
            date: event.date || '',
            time: event.time || '',
            venue: event.venue || '',
            address: event.address || '',
            max_capacity: event.max_capacity || '',
            organizer: event.organizer || '',
            status: event.status || 'Upcoming',
        })
        form.reset()
    } else {
        form.reset()
    }
})

function submit() {
    if (props.isEditing && props.event) {
        form.put(`/events/${props.event.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('updated', form.data())
                emit('close')
                form.reset()
            },
        })
    } else {
        form.post('/events', {
            preserveScroll: true,
            onSuccess: () => {
                emit('created', form.data())
                emit('close')
                form.reset()
            },
        })
    }
}

function cancel() {
    emit('close')
    form.reset()
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="cancel">
        <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle class="text-2xl font-bold tracking-tight">
                    {{ isEditing ? 'Edit Event' : 'Create New Event' }}
                </DialogTitle>
                <DialogDescription class="text-lg">
                    {{ isEditing
                        ? 'Update the event details and settings'
                        : 'Fill in the details to create a new event'
                    }}
                </DialogDescription>
            </DialogHeader>

            <!-- Error Alert -->
            <Alert v-if="form.errors && Object.keys(form.errors).length" variant="destructive">
                <AlertDescription>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                    </ul>
                </AlertDescription>
            </Alert>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information Card -->
                <Card>
                    <CardContent class="pt-6 space-y-4">
                        <div class="space-y-2">
                            <Label for="title" class="text-sm font-medium">Event Title</Label>
                            <Input id="title" v-model="form.title" placeholder="Enter event title" :required="true"
                                class="w-full" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description" class="text-sm font-medium">Description</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Describe your event..."
                                rows="4" class="w-full resize-none" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Date & Time Card -->
                <Card>
                    <CardContent class="pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="date" class="text-sm font-medium flex items-center gap-2">
                                    <Calendar class="h-4 w-4" />
                                    Event Date
                                </Label>
                                <Input id="date" v-model="form.date" type="date" :required="true" />
                            </div>

                            <div class="space-y-2">
                                <Label for="time" class="text-sm font-medium flex items-center gap-2">
                                    <Clock class="h-4 w-4" />
                                    Event Time
                                </Label>
                                <Input id="time" v-model="form.time" type="time" :required="true" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Location Card -->
                <Card>
                    <CardContent class="pt-6 space-y-4">
                        <div class="space-y-2">
                            <Label for="venue" class="text-sm font-medium flex items-center gap-2">
                                <MapPin class="h-4 w-4" />
                                Venue
                            </Label>
                            <Input id="venue" v-model="form.venue" placeholder="Enter venue name" :required="true" />
                        </div>

                        <div class="space-y-2">
                            <Label for="address" class="text-sm font-medium">Full Address</Label>
                            <Input id="address" v-model="form.address" placeholder="Enter complete address" />
                        </div>

                        <!-- Map Preview Placeholder -->
                        <div class="bg-muted/50 border rounded-lg p-8 text-center">
                            <MapPin class="w-12 h-12 mx-auto mb-3 text-muted-foreground/60" />
                            <p class="text-muted-foreground text-sm">Location preview will be shown here</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Capacity & Organizer Card -->
                <Card>
                    <CardContent class="pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="max_capacity" class="text-sm font-medium flex items-center gap-2">
                                    <Users class="h-4 w-4" />
                                    Maximum Capacity
                                </Label>
                                <Input id="max_capacity" v-model="form.max_capacity" type="number" placeholder="0"
                                    min="1" />
                            </div>

                            <div class="space-y-2">
                                <Label for="organizer" class="text-sm font-medium">Organizer</Label>
                                <Input id="organizer" v-model="form.organizer" placeholder="Enter organizer name"
                                    :required="true" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Status Card -->
                <Card>
                    <CardContent class="pt-6">
                        <div class="space-y-2">
                            <Label class="text-sm font-medium flex items-center gap-2">
                                <Shield class="h-4 w-4" />
                                Event Status
                            </Label>
                            <div class="flex gap-4">
                                <div class="flex items-center space-x-2">
                                    <Checkbox id="status-upcoming" :checked="form.status === 'Upcoming'"
                                        @update:checked="form.status = 'Upcoming'" />
                                    <Label for="status-upcoming" class="text-sm font-normal">Upcoming</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox id="status-ongoing" :checked="form.status === 'Ongoing'"
                                        @update:checked="form.status = 'Ongoing'" />
                                    <Label for="status-ongoing" class="text-sm font-normal">Ongoing</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox id="status-completed" :checked="form.status === 'Completed'"
                                        @update:checked="form.status = 'Completed'" />
                                    <Label for="status-completed" class="text-sm font-normal">Completed</Label>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Form Actions -->
                <DialogFooter class="flex justify-between items-center pt-6 border-t">
                    <Button variant="outline" type="button" @click="cancel" class="flex-1 sm:flex-none">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing" class="flex-1 sm:flex-none">
                        <span v-if="form.processing">
                            {{ isEditing ? 'Updating...' : 'Creating...' }}
                        </span>
                        <span v-else>
                            {{ isEditing ? 'Update Event' : 'Create Event' }}
                        </span>
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>