<template>
  <AppLayout>
    <Head title="Help & Support" />

    <div class="p-4 sm:p-6">
      <!-- Header -->
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Help & Support</h1>
        
        <!-- Quick Actions -->
        <div class="flex space-x-2">
          <button 
            @click="resetForm"
            class="rounded-xl bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition duration-200 text-sm sm:text-base"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Ticket
          </button>
        </div>
      </div>

<!-- Status Message with Close Button -->
<div v-if="$page.props.flash?.status && showStatus" class="mb-6 relative">
  <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl flex items-center">
    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
    </svg>
    <span class="flex-1 pr-6 sm:pr-0">{{ $page.props.flash.status }}</span>

    <!-- Close Button -->
    <button 
      @click="showStatus = false"
      class="absolute top-3 right-3 flex-shrink-0 text-green-700 hover:text-green-800 dark:text-green-300 dark:hover:text-green-200 transition duration-200"
      aria-label="Close message"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
  </div>
</div>


      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Support Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Submit New Ticket</h2>
          
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <InputLabel for="subject" value="Subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" />
              <TextInput
                id="subject"
                type="text"
                class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition duration-200"
                placeholder="Brief description of your issue"
                v-model="form.subject"
                required
                autofocus
                autocomplete="off"
              />
              <InputError class="mt-1" :message="form.errors.subject" />
            </div>

            <div class="grid grid-cols-1 gap-4">
              <div>
                <InputLabel for="category" value="Category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" />
                <select
                  id="category"
                  class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition duration-200"
                  v-model="form.category"
                  required
                >
                  <option value="technical">Technical Issue</option>
                  <option value="billing">Billing Inquiry</option>
                  <option value="account">Account Issue</option>
                  <option value="feature_request">Feature Request</option>
                  <option value="other">Other</option>
                </select>
                <InputError class="mt-1" :message="form.errors.category" />
              </div>

              <div>
                <InputLabel for="priority" value="Priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" />
                <select
                  id="priority"
                  class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition duration-200"
                  v-model="form.priority"
                  required
                >
                  <option value="low">Low Priority</option>
                  <option value="medium">Medium Priority</option>
                  <option value="high">High Priority</option>
                  <option value="urgent">Urgent</option>
                </select>
                <InputError class="mt-1" :message="form.errors.priority" />
              </div>
            </div>

            <div>
              <InputLabel for="message" value="Message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" />
              <textarea
                id="message"
                class="w-full px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition duration-200"
                rows="4"
                placeholder="Please describe your issue in detail..."
                v-model="form.message"
                required
                autocomplete="off"
              ></textarea>
              <InputError class="mt-1" :message="form.errors.message" />
            </div>

            <div class="flex items-center justify-end pt-4">
              <PrimaryButton 
                :class="['px-4 sm:px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition duration-200 text-sm sm:text-base', 
                        { 'opacity-50 cursor-not-allowed': form.processing }]" 
                :disabled="form.processing"
              >
                <span v-if="!form.processing">Submit Ticket</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
              </PrimaryButton>
            </div>
          </form>
        </div>

        <!-- Tickets List Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Support Tickets</h2>
          
          <!-- Filters -->
          <div class="mb-4 flex flex-col sm:flex-row gap-2">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search tickets..."
              class="flex-1 rounded-xl border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
              autocomplete="off"
            />
            
            <select
              v-model="filterStatus"
              class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
              <option value="">All Status</option>
              <option value="open">Open</option>
              <option value="in_progress">In Progress</option>
              <option value="resolved">Resolved</option>
              <option value="closed">Closed</option>
            </select>
          </div>

          <!-- Responsive Tickets List -->
          <div class="overflow-hidden">
            <div v-if="filteredTickets.length === 0" class="text-center py-8">
              <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">No support tickets found</p>
            </div>

            <div v-else class="space-y-3 max-h-96 overflow-y-auto">
              <div 
                v-for="ticket in paginatedTickets" 
                :key="ticket.id"
                class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 border border-gray-200 dark:border-gray-600 hover:shadow-md transition duration-200 cursor-pointer"
                @click="openTicketModal(ticket)"
              >
                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                  <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate flex-1 min-w-[120px]">
                    {{ ticket.subject }}
                  </h3>
                  <span :class="[
                    'rounded-full px-2 py-1 text-xs font-semibold shrink-0',
                    ticket.status === 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                    ticket.status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                    ticket.status === 'open' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                  ]">
                    {{ ticket.status.replace('_', ' ') }}
                  </span>
                </div>

                <div class="flex flex-wrap gap-2 mb-2">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium capitalize bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ ticket.category.replace('_', ' ') }}
                  </span>
                  <span :class="[
                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium capitalize',
                    ticket.priority === 'low' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                    ticket.priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                    ticket.priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' :
                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                  ]">
                    {{ ticket.priority }}
                  </span>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-2">
                  {{ ticket.message }}
                </p>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                  {{ formatDateTime(ticket.created_at) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Pagination Controls -->
          <div v-if="filteredTickets.length > 0" class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-600 dark:text-gray-400">
            <div>
              <p class="text-sm">Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredTickets.length }} tickets</p>
            </div>
            <div class="flex flex-wrap gap-1">
              <button 
                @click="prevPage" 
                :disabled="currentPage === 1"
                :class="['px-3 py-1 rounded transition duration-200 text-sm', 
                         currentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-700' : 
                         'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600']"
              >
                ← Prev
              </button>
              
              <div class="flex flex-wrap gap-1">
                <button 
                  v-for="page in visiblePages" 
                  :key="page"
                  @click="goToPage(page)"
                  :class="['px-2 py-1 rounded transition duration-200 text-sm min-w-[32px]',
                           currentPage === page ? 
                           'bg-blue-600 text-white' : 
                           'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600']"
                >
                  {{ page }}
                </button>
              </div>
              
              <button 
                @click="nextPage" 
                :disabled="currentPage === totalPages"
                :class="['px-3 py-1 rounded transition duration-200 text-sm', 
                         currentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-700' : 
                         'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600']"
              >
                Next →
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Ticket Detail Modal -->
      <div v-if="selectedTicket" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click="closeModalOnOutsideClick">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-hidden">
          <!-- Modal Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
              Ticket Details
            </h2>
            <button 
              @click="selectedTicket = null"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition duration-200"
              aria-label="Close modal"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Modal Content -->
          <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <!-- Ticket Header Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                  {{ selectedTicket.subject }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ formatDateTime(selectedTicket.created_at) }}
                </p>
              </div>
              
              <!-- Badges Container - Moved inside the grid div -->
              <div class="flex flex-wrap items-center gap-2">
                <!-- Status Badge -->
                <span :class="[
                  'rounded-full px-3 py-1 text-sm font-semibold inline-flex items-center border',
                  selectedTicket.status === 'resolved' ? 'bg-green-500/10 text-green-700 dark:bg-green-500/20 dark:text-green-300 border-green-500/30' :
                  selectedTicket.status === 'in_progress' ? 'bg-blue-500/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border-blue-500/30' :
                  selectedTicket.status === 'open' ? 'bg-yellow-500/10 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300 border-yellow-500/30' :
                  'bg-gray-500/10 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300 border-gray-500/30'
                ]">
                  {{ selectedTicket.status.replace('_', ' ') }}
                </span>
                
                <!-- Category Badge -->
                <span :class="[
                  'rounded-full px-3 py-1 text-sm font-semibold inline-flex items-center border',
                  selectedTicket.category === 'technical' ? 'bg-purple-500/10 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border-purple-500/30' :
                  selectedTicket.category === 'billing' ? 'bg-pink-500/10 text-pink-700 dark:bg-pink-500/20 dark:text-pink-300 border-pink-500/30' :
                  selectedTicket.category === 'account' ? 'bg-indigo-500/10 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 border-indigo-500/30' :
                  selectedTicket.category === 'feature_request' ? 'bg-teal-500/10 text-teal-700 dark:bg-teal-500/20 dark:text-teal-300 border-teal-500/30' :
                  'bg-gray-500/10 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300 border-gray-500/30'
                ]">
                  {{ selectedTicket.category.replace('_', ' ') }}
                </span>
                
                <!-- Priority Badge -->
                <span :class="[
                  'rounded-full px-3 py-1 text-sm font-semibold inline-flex items-center border',
                  selectedTicket.priority === 'low' ? 'bg-green-500/10 text-green-700 dark:bg-green-500/20 dark:text-green-300 border-green-500/30' :
                  selectedTicket.priority === 'medium' ? 'bg-yellow-500/10 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300 border-yellow-500/30' :
                  selectedTicket.priority === 'high' ? 'bg-orange-500/10 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300 border-orange-500/30' :
                  'bg-red-500/10 text-red-700 dark:bg-red-500/20 dark:text-red-300 border-red-500/30'
                ]">
                  {{ selectedTicket.priority }} Priority
                </span>
              </div>
            </div>

            <!-- Ticket Message -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Message</h4>
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ selectedTicket.message }}</p>
              </div>
            </div>

            <!-- Ticket Metadata -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Ticket ID:</span>
                <span class="text-gray-600 dark:text-gray-400 ml-2">#{{ selectedTicket.id }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Submitted:</span>
                <span class="text-gray-600 dark:text-gray-400 ml-2">{{ formatDateTime(selectedTicket.created_at) }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Last Updated:</span>
                <span class="text-gray-600 dark:text-gray-400 ml-2">{{ formatDateTime(selectedTicket.updated_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="flex justify-end p-6 border-t border-gray-200 dark:border-gray-700">
            <button 
              @click="selectedTicket = null"
              class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition duration-200"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import InputLabel from '../../components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { ref, computed, onMounted, nextTick } from 'vue';

const props = defineProps({
  tickets: {
    type: Array,
    default: () => [],
  },
});

// Store initial form values
const initialFormState = {
  subject: '',
  message: '',
  category: 'technical',
  priority: 'medium',
};

const form = useForm({ ...initialFormState });
const showStatus = ref(true);
const selectedTicket = ref(null);
let statusTimeout = null;

const searchQuery = ref('');
const filterStatus = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(5);

// Open ticket modal
const openTicketModal = (ticket) => {
  selectedTicket.value = ticket;
};

// Close ticket modal when clicking outside
const closeModalOnOutsideClick = (event) => {
  if (event.target.classList.contains('bg-black')) {
    selectedTicket.value = null;
  }
};

// Simplified pagination for mobile
const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 3;
  
  let start = Math.max(1, currentPage.value - 1);
  const end = Math.min(totalPages.value, start + maxVisible - 1);
  
  start = Math.max(1, end - maxVisible + 1);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

const filteredTickets = computed(() => {
  return props.tickets.filter(ticket => {
    const matchesSearch = ticket.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         ticket.message.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    const matchesStatus = filterStatus.value ? ticket.status === filterStatus.value : true;
    
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => {
  return Math.ceil(filteredTickets.value.length / itemsPerPage.value);
});

const paginatedTickets = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredTickets.value.slice(start, end);
});

const startIndex = computed(() => {
  return (currentPage.value - 1) * itemsPerPage.value;
});

const endIndex = computed(() => {
  return Math.min(startIndex.value + itemsPerPage.value, filteredTickets.value.length);
});

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const goToPage = (page) => {
  currentPage.value = page;
};

// Function to completely reset the form
const resetForm = () => {
  form.subject = '';
  form.message = '';
  form.category = 'technical';
  form.priority = 'medium';
  form.clearErrors();
  
  nextTick(() => {
    const subjectInput = document.getElementById('subject');
    if (subjectInput) {
      subjectInput.focus();
    }
  });
};

// Reset to first page when filters change
onMounted(() => {
  currentPage.value = 1;
});

const submit = () => {
  form.post(route('help-support.store'), {
    onFinish: () => {
      resetForm();
      showStatus.value = true;
      
      if (statusTimeout) {
        clearTimeout(statusTimeout);
      }
      statusTimeout = setTimeout(() => {
        showStatus.value = false;
      }, 5000);
    },
  });
};

// Format date with 12-hour time (AM/PM)
const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
  box-orient: vertical;
}

/* Modal animation */
.fixed {
  transition: opacity 0.3s ease;
}

/* Scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
  background: #374151;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}
</style>