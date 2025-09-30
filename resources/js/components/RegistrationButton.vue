<!-- resources/js/components/RegistrationButton.vue -->
<script setup lang="ts">
import { ref, computed } from 'vue'
import { UserPlus, UserMinus, Clock, Check } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import RegistrationModal from '@/components/RegistrationModal.vue'



interface Props {
    event: {
        id: number
        title: string
        registered_count: number
        max_capacity: number
        has_available_spots: boolean
    }
    isUserRegistered?: boolean
    isUserWaitlisted?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    isUserRegistered: false,
    isUserWaitlisted: false
})

const emit = defineEmits<{
    updated: []
}>()

const isLoading = ref(false)
const showSuccess = ref(false)
const showRegistrationModal = ref(false)

// Computed properties
const registrationStatus = computed(() => {
    if (props.isUserRegistered) return 'registered'
    if (props.isUserWaitlisted) return 'waitlisted'
    return 'available'
})

const buttonText = computed(() => {
    switch (registrationStatus.value) {
        case 'registered': return 'Registered'
        case 'waitlisted': return 'Waitlisted'
        default: return 'Register'
    }
})

const buttonVariant = computed(() => {
    switch (registrationStatus.value) {
        case 'registered': return 'secondary'
        case 'waitlisted': return 'outline'
        default: return 'default'
    }
})

const buttonIcon = computed(() => {
    switch (registrationStatus.value) {
        case 'registered': return Check
        case 'waitlisted': return Clock
        default: return UserPlus
    }
})

// Calculate available spots
const availableSpots = computed(() => {
    return Math.max(0, props.event.max_capacity - props.event.registered_count)
})

// Get CSRF token safely
function getCsrfToken(): string {
    const meta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null
    return meta?.content || ''
}

// Registration functions
async function register() {
    if (isLoading.value) return

    isLoading.value = true
    showSuccess.value = false

    try {
        const response = await fetch(`/events/${props.event.id}/register`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })

        const data = await response.json()

        if (data.success) {
            showSuccess.value = true
            setTimeout(() => {
                showSuccess.value = false
            }, 3000)
            emit('updated')
            showRegistrationModal.value = false
        } else {
            alert(data.message || 'Failed to register for event')
        }
    } catch (error) {
        console.error('Registration error:', error)
        alert('An error occurred while registering')
    } finally {
        isLoading.value = false
    }
}

async function unregister() {
    if (!confirm('Are you sure you want to cancel your registration?')) return

    isLoading.value = true

    try {
        const response = await fetch(`/events/${props.event.id}/register`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })

        const data = await response.json()

        if (data.success) {
            emit('updated')
        } else {
            alert(data.message || 'Failed to cancel registration')
        }
    } catch (error) {
        console.error('Unregistration error:', error)
        alert('An error occurred while cancelling registration')
    } finally {
        isLoading.value = false
    }
}

function handleRegisterClick() {
    if (props.isUserRegistered) {
        unregister()
    } else {
        showRegistrationModal.value = true
    }
}
</script>

<template>
    <div class="flex flex-col gap-2">
        <!-- Registration Button -->
        <Button :variant="buttonVariant"
            :disabled="isLoading || (!props.isUserRegistered && !props.event.has_available_spots && !props.isUserWaitlisted)"
            @click="handleRegisterClick" class="w-full">
            <component :is="buttonIcon" class="h-4 w-4 mr-2" />

            <span v-if="isLoading">Processing...</span>
            <span v-else-if="showSuccess" class="flex items-center gap-1">
                <Check class="h-4 w-4" />
                Success!
            </span>
            <span v-else>{{ buttonText }}</span>
        </Button>

        <!-- Status Info -->
        <div class="text-xs text-muted-foreground text-center">
            <span v-if="props.isUserRegistered" class="text-emerald-600">
                ✓ You are registered
            </span>
            <span v-else-if="props.isUserWaitlisted" class="text-amber-600">
                ⏳ You are on waitlist
            </span>
            <span v-else-if="props.event.has_available_spots" class="text-blue-600">
                {{ props.event.registered_count }} / {{ props.event.max_capacity }} registered
            </span>
            <span v-else class="text-red-600">
                ✗ Event full ({{ props.event.registered_count }}/{{ props.event.max_capacity }})
            </span>
        </div>

        <!-- Registration Confirmation Modal -->
        <RegistrationModal :open="showRegistrationModal" title="Confirm Registration"
            :description="`Register for ${event.title}?`" @close="showRegistrationModal = false">
            <div class="space-y-4">
                <p class="text-sm text-muted-foreground">
                    You are about to register for <strong>{{ event.title }}</strong>.
                </p>

                <div class="bg-muted/50 p-3 rounded-lg text-sm">
                    <div class="flex justify-between">
                        <span>Available Spots:</span>
                        <span>{{ availableSpots }} remaining</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Current Registrations:</span>
                        <span>{{ event.registered_count }} / {{ event.max_capacity }}</span>
                    </div>
                </div>
            </div>

            <!-- 👇 Footer slot for buttons -->
            <template #footer>
                <Button variant="outline" @click="showRegistrationModal = false">
                    Cancel
                </Button>
                <Button @click="register" :disabled="isLoading">
                    <span v-if="isLoading">Registering...</span>
                    <span v-else>Confirm Registration</span>
                </Button>
            </template>
        </RegistrationModal>
    </div>
</template>
