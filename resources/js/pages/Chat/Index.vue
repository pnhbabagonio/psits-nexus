<template>
  <AppLayout>
    <Head title="Chat Support" />

    <div class="h-screen flex flex-col p-4 sm:p-6">
      <!-- Header -->
      <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-shrink-0">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Chat Support</h1>

        <!-- Quick Actions -->
        <div class="flex space-x-2">
          <button
            @click="clearChat"
            class="rounded-xl bg-red-600 px-4 py-2 text-white hover:bg-red-700 transition duration-200 text-sm sm:text-base"
            :disabled="messages.length === 0"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Clear Chat
          </button>
        </div>
      </div>

      <!-- Status Message with Close Button -->
      <div v-if="$page.props.status && showStatus" class="mb-4 relative flex-shrink-0">
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl flex items-center">
          <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <span class="flex-1 pr-6 sm:pr-0">{{ $page.props.status }}</span>

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

      <!-- Chat Container -->
      <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden">

        <!-- Chat Messages Area -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4" @scroll="handleScroll">
          <!-- Welcome Message -->
          <div v-if="messages.length === 0" class="text-center py-8">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Welcome to Chat Support!</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
              I'm here to help you with any questions or issues you might have. Just type your message below to get started.
            </p>
          </div>

          <!-- Chat Messages -->
          <div v-for="message in messages" :key="message.id" class="flex" :class="message.sender === 'user' ? 'justify-end' : 'justify-start'">
            <div :class="[
              'max-w-xs sm:max-w-md lg:max-w-lg xl:max-w-xl rounded-2xl px-4 py-3 break-words',
              message.sender === 'user'
                ? 'bg-blue-600 text-white rounded-br-md'
                : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-bl-md'
            ]">
              <!-- Bot Avatar -->
              <div v-if="message.sender === 'bot'" class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm leading-relaxed">{{ message.message }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    {{ formatTime(message.created_at) }}
                  </p>
                </div>
              </div>

              <!-- User Message -->
              <div v-else>
                <p class="text-sm leading-relaxed">{{ message.message }}</p>
                <p class="text-xs text-blue-100 mt-2 text-right">
                  {{ formatTime(message.created_at) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="flex justify-start">
            <div class="max-w-xs rounded-2xl rounded-bl-md px-4 py-3 bg-gray-100 dark:bg-gray-700">
              <div class="flex items-center space-x-1">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="flex space-x-1">
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Message Input Area -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50">
          <form @submit.prevent="sendMessage" class="flex space-x-4">
            <div class="flex-1">
              <textarea
                ref="messageInput"
                v-model="form.message"
                placeholder="Type your message..."
                rows="1"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition duration-200 resize-none"
                :disabled="form.processing"
                @keydown.enter.prevent="handleEnterKey"
                @input="adjustTextareaHeight"
                autocomplete="off"
                maxlength="1000"
              ></textarea>
              <InputError class="mt-1" :message="form.errors.message" />
            </div>

            <button
              type="submit"
              :disabled="form.processing || !form.message.trim()"
              :class="[
                'px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center justify-center min-w-[100px]',
                form.processing || !form.message.trim()
                  ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'
                  : 'bg-blue-600 hover:bg-blue-700 text-white'
              ]"
            >
              <span v-if="!form.processing" class="flex items-center">
                Send
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
              </span>
            </button>
          </form>

          <!-- Character count -->
          <div class="mt-2 text-right">
            <span class="text-xs text-gray-500 dark:text-gray-400">
              {{ form.message.length }}/1000
            </span>
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
import { ref, nextTick, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  messages: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  message: '',
});

const showStatus = ref(true);
const isTyping = ref(false);
const messagesContainer = ref(null);
const messageInput = ref(null);
const shouldAutoScroll = ref(true);

// Handle enter key in textarea
const handleEnterKey = (event) => {
  if (!event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};

// Auto-resize textarea
const adjustTextareaHeight = () => {
  const textarea = messageInput.value;
  if (textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';
  }
};

// Handle scroll to detect if user is at bottom
const handleScroll = () => {
  const container = messagesContainer.value;
  if (container) {
    const { scrollTop, scrollHeight, clientHeight } = container;
    shouldAutoScroll.value = scrollTop + clientHeight >= scrollHeight - 10;
  }
};

// Scroll to bottom of messages
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value && shouldAutoScroll.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

// Send message
const sendMessage = () => {
  if (!form.message.trim() || form.processing) return;

  // Show typing indicator
  isTyping.value = true;

  form.post(route('chat.store'), {
    onFinish: () => {
      form.reset();
      form.message = ''; // 🔥 force clear input
      isTyping.value = false;
      scrollToBottom();

      // Reset textarea height
      nextTick(() => {
        if (messageInput.value) {
          messageInput.value.style.height = 'auto';
          messageInput.value.focus(); // 👈 keep focus here
        }
      });
    },
    onError: () => {
      isTyping.value = false;
    }
  });
};

// Clear chat history
const clearChat = () => {
  if (props.messages.length === 0) return;

  if (confirm('Are you sure you want to clear your chat history? This action cannot be undone.')) {
    form.post(route('chat.clear'), {
      method: 'delete',
    });
  }
};

// Format time for display
const formatTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};

// Auto-scroll on mount and when messages change
onMounted(() => {
  scrollToBottom();
  if (messageInput.value) {
    messageInput.value.focus();
  }
});

// Hide status message after 5 seconds
let statusTimeout = null;
if (showStatus.value) {
  statusTimeout = setTimeout(() => {
    showStatus.value = false;
  }, 5000);
}

onBeforeUnmount(() => {
  if (statusTimeout) {
    clearTimeout(statusTimeout);
  }
});
</script>

<style scoped>
/* Custom scrollbar for messages */
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

/* Smooth scrolling */
.overflow-y-auto {
  scroll-behavior: smooth;
}

/* Animation for typing indicator */
@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0,0,0);
  }
  40%, 43% {
    transform: translate3d(0, -8px, 0);
  }
  70% {
    transform: translate3d(0, -4px, 0);
  }
  90% {
    transform: translate3d(0, -2px, 0);
  }
}

.animate-bounce {
  animation: bounce 1.4s infinite;
}
</style>
