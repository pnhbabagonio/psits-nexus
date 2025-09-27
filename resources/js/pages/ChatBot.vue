<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Send, Bot, User, Clock, HelpCircle } from 'lucide-vue-next';
import { ref, computed, nextTick } from 'vue';

interface ChatMessage {
  id: number;
  content: string;
  isBot: boolean;
  timestamp: Date;
  quickReplies?: string[];
}

interface FAQItem {
  question: string;
  answer: string;
  keywords: string[];
  quickReplies?: string[];
}

// Define props interface
interface Props {
  initialMessage?: string;
}

const props = withDefaults(defineProps<Props>(), {
  initialMessage: 'Hello! I\'m your PSITS Nexus assistant. How can I help you today?'
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Chatbot',
    href: '/chatbot',
  },
];

const messages = ref<ChatMessage[]>([
  {
    id: 1,
    content: props.initialMessage,
    isBot: true,
    timestamp: new Date(),
    quickReplies: [
      'Payment issues',
      'Event registration',
      'Membership status',
      'QR code problems'
    ]
  }
]);

const userInput = ref('');
const isTyping = ref(false);
const chatContainer = ref<HTMLElement>();

// FAQ database
const faqDatabase: FAQItem[] = [
  {
    question: 'payment',
    answer: 'For payment-related issues, you can check your payment status in the "My Payments" section. If you have unpaid fees, you can pay via QR code or bank transfer. Payment confirmation usually takes 1-2 business days.',
    keywords: ['payment', 'fee', 'pay', 'money', 'transaction', 'bill', 'due'],
    quickReplies: ['How to pay?', 'Payment status', 'Payment confirmation']
  },
  {
    question: 'qr code',
    answer: 'QR code issues can be resolved by ensuring you have a stable internet connection and proper lighting when scanning. If the QR code is not working, try generating a new one from the Payments section.',
    keywords: ['qr', 'code', 'scan', 'qr code', 'scanning', 'not working'],
    quickReplies: ['QR not scanning', 'Generate new QR', 'QR expired']
  },
  {
    question: 'events',
    answer: 'You can register for upcoming events through the Events section. Event attendance is tracked via QR code check-in. For event-related questions, contact the events coordinator.',
    keywords: ['event', 'events', 'registration', 'attend', 'meeting', 'workshop'],
    quickReplies: ['Upcoming events', 'Event registration', 'Attendance tracking']
  },
  {
    question: 'membership',
    answer: 'Your membership status can be checked in your profile. Active membership requires updated payments and participation in at least 2 events per semester. For membership upgrades, contact the membership committee.',
    keywords: ['member', 'membership', 'status', 'active', 'inactive', 'renew'],
    quickReplies: ['Check status', 'Renew membership', 'Membership benefits']
  },
  {
    question: 'technical support',
    answer: 'For technical issues with the PSITS Nexus platform, please contact our support team at support@psitsnexus.ph or use the help desk system. Include screenshots of any error messages.',
    keywords: ['technical', 'support', 'help', 'bug', 'error', 'issue', 'problem'],
    quickReplies: ['Report bug', 'Contact support', 'Platform issues']
  },
  {
    question: 'deadlines',
    answer: 'Important deadlines for payments, event registrations, and submissions are available in the Calendar section. You\'ll also receive email reminders 3 days before each deadline.',
    keywords: ['deadline', 'due', 'date', 'time', 'submit', 'registration'],
    quickReplies: ['Payment deadlines', 'Event deadlines', 'Submission dates']
  }
];

const commonQuestions = [
  'How do I pay my membership fee?',
  'Where can I find upcoming events?',
  'How to check my payment status?',
  'What should I do if QR code is not working?',
  'How to update my profile information?'
];

const findBestMatch = (query: string): FAQItem | null => {
  const lowerQuery = query.toLowerCase();
  
  // Exact match check
  for (const faq of faqDatabase) {
    if (lowerQuery.includes(faq.question.toLowerCase())) {
      return faq;
    }
  }
  
  // Keyword match
  for (const faq of faqDatabase) {
    for (const keyword of faq.keywords) {
      if (lowerQuery.includes(keyword.toLowerCase())) {
        return faq;
      }
    }
  }
  
  return null;
};

const sendMessage = async () => {
  if (!userInput.value.trim()) return;

  // Add user message
  const userMessage: ChatMessage = {
    id: Date.now(),
    content: userInput.value.trim(),
    isBot: false,
    timestamp: new Date()
  };
  
  messages.value.push(userMessage);
  const userQuery = userInput.value;
  userInput.value = '';
  
  // Show typing indicator
  isTyping.value = true;
  await nextTick();
  scrollToBottom();
  
  // Simulate typing delay
  setTimeout(async () => {
    isTyping.value = false;
    
    let response: string;
    let quickReplies: string[] = [];
    
    const matchedFAQ = findBestMatch(userQuery);
    
    if (matchedFAQ) {
      response = matchedFAQ.answer;
      quickReplies = matchedFAQ.quickReplies || [];
    } else {
      response = 'I understand you\'re asking about: "' + userQuery + '". For more specific assistance, please contact our support team or try one of these common questions:';
      quickReplies = commonQuestions;
    }
    
    const botMessage: ChatMessage = {
      id: Date.now() + 1,
      content: response,
      isBot: true,
      timestamp: new Date(),
      quickReplies: quickReplies.slice(0, 3) // Limit to 3 quick replies
    };
    
    messages.value.push(botMessage);
    await nextTick();
    scrollToBottom();
  }, 1500);
};

const sendQuickReply = (reply: string) => {
  userInput.value = reply;
  sendMessage();
};

const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  }
};

// Auto-scroll when new messages are added
</script>

<template>
  <Head title="Chatbot" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col rounded-xl">
      <!-- Header -->
      <div class="border-b border-border bg-card p-6">
        <div class="flex items-center gap-3">
          <div class="rounded-full bg-gradient-to-r from-blue-500 to-blue-600 p-3">
            <Bot class="h-6 w-6 text-white" />
          </div>
          <div>
            <h1 class="text-2xl font-bold text-foreground">Nexi</h1>
            <p class="text-muted-foreground">Get instant help with Nexi for your common questions</p>
          </div>
        </div>
      </div>

      <!-- Chat Container -->
      <div ref="chatContainer" class="flex-1 overflow-y-auto p-6 space-y-6 bg-muted/20">
        <!-- Messages -->
        <div v-for="message in messages" :key="message.id" class="flex gap-3" :class="message.isBot ? 'justify-start' : 'justify-end'">
          <!-- Bot Message -->
          <div v-if="message.isBot" class="flex gap-3 max-w-[80%]">
            <div class="flex-shrink-0">
              <div class="rounded-full bg-blue-100 p-2">
                <Bot class="h-4 w-4 text-blue-600" />
              </div>
            </div>
            <div class="space-y-2">
              <div class="rounded-2xl rounded-tl-none bg-card p-4 shadow-sm border border-border">
                <p class="text-sm text-foreground whitespace-pre-wrap">{{ message.content }}</p>
              </div>
              <!-- Quick Replies -->
              <div v-if="message.quickReplies && message.quickReplies.length > 0" class="flex flex-wrap gap-2">
                <Button 
                  v-for="(reply, index) in message.quickReplies" 
                  :key="index"
                  variant="outline" 
                  size="sm"
                  class="text-xs rounded-full"
                  @click="sendQuickReply(reply)"
                >
                  {{ reply }}
                </Button>
              </div>
              <div class="flex items-center gap-1 text-xs text-muted-foreground">
                <Clock class="h-3 w-3" />
                <span>{{ message.timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
              </div>
            </div>
          </div>

          <!-- User Message -->
          <div v-else class="flex gap-3 max-w-[80%]">
            <div class="space-y-2 text-right">
              <div class="rounded-2xl rounded-tr-none bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                <p class="text-sm text-white whitespace-pre-wrap">{{ message.content }}</p>
              </div>
              <div class="flex items-center gap-1 justify-end text-xs text-muted-foreground">
                <Clock class="h-3 w-3" />
                <span>{{ message.timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
              </div>
            </div>
            <div class="flex-shrink-0">
              <div class="rounded-full bg-blue-500 p-2">
                <User class="h-4 w-4 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Typing Indicator -->
        <div v-if="isTyping" class="flex gap-3">
          <div class="flex-shrink-0">
            <div class="rounded-full bg-blue-100 p-2">
              <Bot class="h-4 w-4 text-blue-600" />
            </div>
          </div>
          <div class="rounded-2xl rounded-tl-none bg-card p-4 shadow-sm border border-border">
            <div class="flex gap-1">
              <div class="h-2 w-2 rounded-full bg-muted-foreground animate-bounce"></div>
              <div class="h-2 w-2 rounded-full bg-muted-foreground animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="h-2 w-2 rounded-full bg-muted-foreground animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="border-t border-border bg-card p-6">
        <div class="flex gap-3">
          <Input
            v-model="userInput"
            type="text"
            placeholder="Type your question here..."
            class="flex-1"
            @keypress.enter="sendMessage"
          />
          <Button 
            @click="sendMessage" 
            :disabled="!userInput.trim()"
            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700"
          >
            <Send class="h-4 w-4" />
          </Button>
        </div>
        
        <!-- Quick Suggestions -->
        <div class="mt-3">
          <p class="text-xs text-muted-foreground mb-2 flex items-center gap-1">
            <HelpCircle class="h-3 w-3" />
            Quick suggestions:
          </p>
          <div class="flex flex-wrap gap-2">
            <Button 
              v-for="(question, index) in commonQuestions.slice(0, 3)" 
              :key="index"
              variant="outline" 
              size="sm"
              class="text-xs rounded-full"
              @click="sendQuickReply(question)"
            >
              {{ question }}
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Custom scrollbar for chat container */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>