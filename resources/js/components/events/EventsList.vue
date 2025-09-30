<script setup lang="ts">
import { ref, computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Badge } from '@/components/ui/badge'
import { Plus, Search, Edit, Trash2, MapPin, Calendar, Users } from 'lucide-vue-next'

// Types
interface Event {
    id: number
    title: string
    description: string
    date: string
    time: string
    location: string
    capacity: number
    registered: number
    status: 'upcoming' | 'ongoing' | 'completed' | 'cancelled'
    category: string
}

// Sample data
const events = ref<Event[]>([
    {
        id: 1,
        title: 'Web Development Workshop',
        description: 'Learn modern web development with Vue.js and Tailwind CSS',
        date: '2025-10-15',
        time: '14:00',
        location: 'Computer Lab 1',
        capacity: 50,
        registered: 35,
        status: 'upcoming',
        category: 'Workshop'
    },
    {
        id: 2,
        title: 'Annual Tech Conference',
        description: 'Join us for the biggest tech event of the year',
        date: '2025-11-20',
        time: '09:00',
        location: 'Main Auditorium',
        capacity: 200,
        registered: 150,
        status: 'upcoming',
        category: 'Conference'
    },
    {
        id: 3,
        title: 'Coding Bootcamp',
        description: 'Intensive 3-day coding bootcamp for beginners',
        date: '2025-09-20',
        time: '10:00',
        location: 'IT Building',
        capacity: 30,
        registered: 30,
        status: 'completed',
        category: 'Training'
    }
])

// State
const searchQuery = ref('')
const isDialogOpen = ref(false)
const isDeleteDialogOpen = ref(false)
const isEditing = ref(false)
const eventToDelete = ref<number | null>(null)

// Form state
const form = ref({
    id: 0,
    title: '',
    description: '',
    date: '',
    time: '',
    location: '',
    capacity: 0,
    registered: 0,
    status: 'upcoming' as Event['status'],
    category: ''
})

// Computed
const filteredEvents = computed(() => {
    if (!searchQuery.value) return events.value
    return events.value.filter(event =>
        event.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        event.location.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        event.category.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

// Methods
const openCreateDialog = () => {
    isEditing.value = false
    form.value = {
        id: 0,
        title: '',
        description: '',
        date: '',
        time: '',
        location: '',
        capacity: 0,
        registered: 0,
        status: 'upcoming',
        category: ''
    }
    isDialogOpen.value = true
}

const openEditDialog = (event: Event) => {
    isEditing.value = true
    form.value = { ...event }
    isDialogOpen.value = true
}

const saveEvent = () => {
    if (isEditing.value) {
        const index = events.value.findIndex(e => e.id === form.value.id)
        if (index !== -1) {
            events.value[index] = { ...form.value }
        }
    } else {
        const newEvent = {
            ...form.value,
            id: Math.max(...events.value.map(e => e.id), 0) + 1
        }
        events.value.push(newEvent)
    }
    isDialogOpen.value = false
}

const openDeleteDialog = (id: number) => {
    eventToDelete.value = id
    isDeleteDialogOpen.value = true
}

const deleteEvent = () => {
    if (eventToDelete.value) {
        events.value = events.value.filter(e => e.id !== eventToDelete.value)
        isDeleteDialogOpen.value = false
        eventToDelete.value = null
    }
}

const getStatusColor = (status: Event['status']) => {
    const colors = {
        upcoming: 'bg-blue-500/10 text-blue-500 hover:bg-blue-500/20',
        ongoing: 'bg-green-500/10 text-green-500 hover:bg-green-500/20',
        completed: 'bg-gray-500/10 text-gray-400 hover:bg-gray-500/20',
        cancelled: 'bg-red-500/10 text-red-500 hover:bg-red-500/20'
    }
    return colors[status]
}

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}
</script>

<template>
    <div class="space-y-4">
        <!-- Header Actions -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    placeholder="Search events..."
                    class="pl-10"
                />
            </div>
            <Button @click="openCreateDialog" class="gap-2">
                <Plus class="w-4 h-4" />
                Create Event
            </Button>
        </div>

        <!-- Events Table -->
        <div class="border rounded-lg bg-card">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Event</TableHead>
                        <TableHead>Date & Time</TableHead>
                        <TableHead>Location</TableHead>
                        <TableHead>Capacity</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="event in filteredEvents" :key="event.id">
                        <TableCell>
                            <div>
                                <div class="font-medium text-foreground">{{ event.title }}</div>
                                <div class="text-sm text-muted-foreground">{{ event.category }}</div>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2 text-sm">
                                <Calendar class="w-4 h-4 text-muted-foreground" />
                                <span>{{ formatDate(event.date) }} at {{ event.time }}</span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2 text-sm">
                                <MapPin class="w-4 h-4 text-muted-foreground" />
                                <span>{{ event.location }}</span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2 text-sm">
                                <Users class="w-4 h-4 text-muted-foreground" />
                                <span>{{ event.registered }}/{{ event.capacity }}</span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <Badge :class="getStatusColor(event.status)" class="capitalize">
                                {{ event.status }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="openEditDialog(event)"
                                >
                                    <Edit class="w-4 h-4" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="openDeleteDialog(event.id)"
                                >
                                    <Trash2 class="w-4 h-4 text-destructive" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Edit Event' : 'Create New Event' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEditing ? 'Update the event details below' : 'Fill in the details to create a new event' }}
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="title">Event Title</Label>
                        <Input id="title" v-model="form.title" placeholder="Enter event title" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="form.description" placeholder="Enter event description" rows="3" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="date">Date</Label>
                            <Input id="date" v-model="form.date" type="date" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="time">Time</Label>
                            <Input id="time" v-model="form.time" type="time" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="location">Location</Label>
                        <Input id="location" v-model="form.location" placeholder="Enter event location" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="category">Category</Label>
                            <Input id="category" v-model="form.category" placeholder="e.g., Workshop, Conference" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="capacity">Capacity</Label>
                            <Input id="capacity" v-model.number="form.capacity" type="number" placeholder="Max attendees" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="upcoming">Upcoming</SelectItem>
                                <SelectItem value="ongoing">Ongoing</SelectItem>
                                <SelectItem value="completed">Completed</SelectItem>
                                <SelectItem value="cancelled">Cancelled</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="isDialogOpen = false">Cancel</Button>
                    <Button @click="saveEvent">{{ isEditing ? 'Update' : 'Create' }} Event</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="isDeleteDialogOpen">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                    <AlertDialogDescription>
                        This action cannot be undone. This will permanently delete the event
                        and all associated data.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction @click="deleteEvent" class="bg-destructive hover:bg-destructive/90">
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>