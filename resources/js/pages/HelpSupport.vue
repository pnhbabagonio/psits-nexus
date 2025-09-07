<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Search,
    MessageSquare,
    Calendar,
    Send,
    HelpCircle,
    Bug,
    CreditCard,
    Settings,
    Lightbulb,
    FileText,
    CheckCircle,
    X
} from 'lucide-vue-next';

// Define types
interface SupportTicket {
    id: number;
    subject: string;
    message: string;
    category: 'technical' | 'billing' | 'account' | 'feature_request' | 'other';
    priority: 'low' | 'medium' | 'high' | 'urgent';
    status: 'open' | 'in_progress' | 'resolved' | 'closed';
    created_at: string;
    updated_at: string;
}

interface Props {
    userTickets?: SupportTicket[];
}

// Get props from Laravel
const props = defineProps<Props>();

// Get flash messages - fix TypeScript error
const page = usePage();
const flashMessage = computed(() => {
    const flash = page.props.flash as any;
    return flash?.message;
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Support', href: '/help-support' }
];

// Form state
const form = useForm({
    subject: '',
    message: '',
    category: '',
    priority: 'medium'
});

// Reactive state
const searchQuery = ref('');
const showSuccessMessage = ref(false);

// Computed properties
const filteredTickets = computed(() => {
    if (!props.userTickets) return [];
    if (!searchQuery.value) return props.userTickets;
    
    return props.userTickets.filter(ticket => 
        ticket.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        ticket.message.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Category options with icons and descriptions
const categoryOptions = [
    {
        value: 'technical',
        label: 'Technical Issue',
        icon: Bug,
        description: 'Problems with features, bugs, or system errors'
    },
    {
        value: 'billing',
        label: 'Billing',
        icon: CreditCard,
        description: 'Payment issues, charges, or membership fees'
    },
    {
        value: 'account',
        label: 'Account',
        icon: Settings,
        description: 'Profile, login, or account settings issues'
    },
    {
        value: 'feature_request',
        label: 'Feature Request',
        icon: Lightbulb,
        description: 'Suggestions for new features or improvements'
    },
    {
        value: 'other',
        label: 'Other',
        icon: HelpCircle,
        description: 'General questions or other concerns'
    }
];

// Utility functions
const getPriorityColor = (priority: string) => {
    switch (priority) {
        case 'urgent': return 'bg-red-100 text-red-700 border-red-200';
        case 'high': return 'bg-orange-100 text-orange-700 border-orange-200';
        case 'medium': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case 'low': return 'bg-gray-100 text-gray-700 border-gray-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'open': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'in_progress': return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'resolved': return 'bg-green-100 text-green-700 border-green-200';
        case 'closed': return 'bg-gray-100 text-gray-700 border-gray-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
};

const getCategoryInfo = (category: string) => {
    return categoryOptions.find(option => option.value === category) || categoryOptions[4];
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const handleSubmit = () => {
    form.post('/help-support', {
        onSuccess: () => {
            form.reset();
            showSuccessMessage.value = true;
            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 5000);
        },
        onError: () => {
            // Errors are automatically handled by the form validation
        }
    });
};

const hideSuccessMessage = () => {
    showSuccessMessage.value = false;
};
</script>

<template>
    <Head title="Submit Support Ticket" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
            <!-- Success Message -->
            <div 
                v-if="showSuccessMessage || flashMessage" 
                class="rounded-md bg-green-50 p-4 border border-green-200 relative"
            >
                <div class="flex items-start gap-3">
                    <CheckCircle class="h-5 w-5 text-green-600 mt-0.5 flex-shrink-0" />
                    <div class="flex-1">
                        <p class="text-sm text-green-700 font-medium">Success!</p>
                        <p class="text-sm text-green-700 mt-1">
                            {{ flashMessage || 'Your support ticket has been submitted successfully!' }}
                        </p>
                    </div>
                    <button 
                        @click="hideSuccessMessage"
                        class="text-green-600 hover:text-green-800 p-1"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <!-- Header Section -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-foreground">Need Help?</h1>
                <p class="mt-2 text-muted-foreground">Submit a support ticket and our team will assist you promptly</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Submit New Ticket -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="h-5 w-5" />
                            Submit New Ticket
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="handleSubmit" class="space-y-4">
                            <div>
                                <label class="text-sm font-medium mb-2 block">Subject *</label>
                                <Input 
                                    v-model="form.subject" 
                                    placeholder="Brief description of your issue"
                                    required
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.subject }"
                                />
                                <p v-if="form.errors.subject" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.subject }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium mb-2 block">Category *</label>
                                <Select v-model="form.category" required>
                                    <SelectTrigger :class="{ 'border-red-500 focus:border-red-500': form.errors.category }">
                                        <SelectValue placeholder="Select category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem 
                                            v-for="option in categoryOptions" 
                                            :key="option.value" 
                                            :value="option.value"
                                            class="cursor-pointer"
                                        >
                                            <div class="flex items-center gap-3">
                                                <component :is="option.icon" class="h-4 w-4" />
                                                <div>
                                                    <div class="font-medium">{{ option.label }}</div>
                                                    <div class="text-xs text-muted-foreground">{{ option.description }}</div>
                                                </div>
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.category }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium mb-2 block">Priority</label>
                                <Select v-model="form.priority">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select priority" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="low">
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                                Low - General questions or minor issues
                                            </div>
                                        </SelectItem>
                                        <SelectItem value="medium">
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 w-2 rounded-full bg-yellow-400"></div>
                                                Medium - Standard issues affecting usage
                                            </div>
                                        </SelectItem>
                                        <SelectItem value="high">
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 w-2 rounded-full bg-orange-400"></div>
                                                High - Significant problems
                                            </div>
                                        </SelectItem>
                                        <SelectItem value="urgent">
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 w-2 rounded-full bg-red-400"></div>
                                                Urgent - Critical issues blocking work
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium mb-2 block">Message *</label>
                                <Textarea 
                                    v-model="form.message" 
                                    placeholder="Please describe your issue in detail. Include any error messages, steps to reproduce the problem, or screenshots if applicable."
                                    rows="6"
                                    required
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.message }"
                                />
                                <p v-if="form.errors.message" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.message }}
                                </p>
                                <p class="text-xs text-muted-foreground mt-1">
                                    The more details you provide, the faster we can help resolve your issue.
                                </p>
                            </div>
                            
                            <Button 
                                type="submit" 
                                class="w-full gap-2" 
                                :disabled="form.processing"
                                :class="form.processing ? 'opacity-50 cursor-not-allowed' : ''"
                            >
                                <Send class="h-4 w-4" />
                                {{ form.processing ? 'Submitting...' : 'Submit Ticket' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- My Tickets -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            My Support Tickets ({{ props.userTickets?.length || 0 }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <!-- Search -->
                        <div class="relative mb-4">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input 
                                v-model="searchQuery"
                                placeholder="Search your tickets..." 
                                class="pl-10"
                            />
                        </div>

                        <!-- Tickets List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <div
                                v-for="ticket in filteredTickets"
                                :key="ticket.id"
                                class="rounded-lg border border-border bg-muted/30 p-3 transition-all hover:bg-muted/50 cursor-pointer"
                            >
                                <div class="space-y-2">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-medium text-sm leading-tight">{{ ticket.subject }}</h4>
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <Badge :class="getPriorityColor(ticket.priority)" class="text-xs">
                                                {{ ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1) }}
                                            </Badge>
                                            <Badge :class="getStatusColor(ticket.status)" class="text-xs">
                                                {{ ticket.status.replace('_', ' ').charAt(0).toUpperCase() + ticket.status.replace('_', ' ').slice(1) }}
                                            </Badge>
                                        </div>
                                    </div>
                                    
                                    <!-- Message Preview -->
                                    <p class="text-xs text-muted-foreground line-clamp-2">
                                        {{ ticket.message }}
                                    </p>
                                    
                                    <!-- Meta Information -->
                                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                                        <div class="flex items-center gap-1">
                                            <component :is="getCategoryInfo(ticket.category).icon" class="h-3 w-3" />
                                            <span>{{ getCategoryInfo(ticket.category).label }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Calendar class="h-3 w-3" />
                                            <span>{{ formatDate(ticket.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Empty State -->
                        <div v-if="filteredTickets.length === 0" class="py-8 text-center">
                            <MessageSquare class="mx-auto h-8 w-8 text-muted-foreground/50" />
                            <p class="mt-2 text-sm text-muted-foreground">
                                {{ (!props.userTickets || props.userTickets.length === 0) ? 'No support tickets yet' : 'No tickets match your search' }}
                            </p>
                            <p v-if="!props.userTickets || props.userTickets.length === 0" class="text-xs text-muted-foreground mt-1">
                                Submit your first ticket using the form on the left
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Help Section -->
            <Card class="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
                <CardContent class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="rounded-full bg-blue-100 p-3">
                            <HelpCircle class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-blue-900 mb-2">Need Immediate Help?</h3>
                            <div class="grid gap-2 text-sm text-blue-800">
                                <p>• For urgent technical issues, please select "Urgent" priority</p>
                                <p>• Include screenshots, error messages, and steps to reproduce the problem</p>
                                <p>• Our support team typically responds within 24 hours for standard issues</p>
                                <p>• Check our FAQ section for common solutions before submitting</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>