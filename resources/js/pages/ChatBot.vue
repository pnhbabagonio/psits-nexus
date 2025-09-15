<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ScrollArea } from '@/components/ui/scroll-area'
import { ref, nextTick } from 'vue'

/* -------------------- Breadcrumbs -------------------- */
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Chatbot', href: '/ChatBot' }
]

/* -------------------- Messages -------------------- */
const messages = ref<{ id: number; sender: 'user' | 'bot'; message: string; created_at: Date }[]>([])
const newMessage = ref('')
const isTyping = ref(false)
const messagesContainer = ref<InstanceType<typeof ScrollArea> | null>(null)

/* -------------------- Helpers -------------------- */
const formatTime = (date: Date) =>
  new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })

const scrollToBottom = () => {
  nextTick(() => {
    const container = messagesContainer.value?.$el.querySelector('[data-radix-scroll-area-viewport]')
    if (container) container.scrollTop = container.scrollHeight
  })
}

/* -------------------- Chat Actions -------------------- */
const botAskCategory = () => {
  const botMessage = {
    id: Date.now(),
    sender: 'bot' as const,
    message: "🤖 Please choose a category by typing its number:\n1. Password Recovery\n2. Signup Problem\n3. Account Setup",
    created_at: new Date(),
  }
  messages.value.push(botMessage)
  scrollToBottom()
}

const sendMessage = () => {
  const userInput = newMessage.value.trim()
  if (!userInput) return

  // Add user message
  const userMessage = {
    id: Date.now(),
    sender: 'user' as const,
    message: userInput,
    created_at: new Date(),
  }
  messages.value.push(userMessage)
  newMessage.value = ''
  scrollToBottom()

  const choice = parseInt(userInput)
  if (![1, 2, 3].includes(choice)) {
    // Invalid input
    setTimeout(() => {
      messages.value.push({
        id: Date.now() + 1,
        sender: 'bot' as const,
        message: '🤖 Invalid choice! Please enter 1, 2, or 3.',
        created_at: new Date(),
      })
      scrollToBottom()
    }, 500)
    return
  }

  // Show typing indicator
  isTyping.value = true

  setTimeout(() => {
    let reply = ''
    switch (choice) {
      case 1:
        reply = '🤖 Password Recovery: You can reset your password by clicking the "Forgot Password" link on the login page.'
        break
      case 2:
        reply = '🤖 Signup Problem: Please make sure all required fields are filled correctly. If the problem persists, contact support.'
        break
      case 3:
        reply = '🤖 Account Setup: Follow the on-screen instructions to set up your account. Make sure your email is verified.'
        break
    }

    messages.value.push({
      id: Date.now() + 1,
      sender: 'bot' as const,
      message: reply,
      created_at: new Date(),
    })
    isTyping.value = false
    scrollToBottom()
  }, 1000)
}

/* -------------------- Clear Chat -------------------- */
const clearChat = () => {
  messages.value = []
  nextTick(() => botAskCategory()) // restart conversation
}

/* -------------------- Start conversation -------------------- */
botAskCategory()
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Chat Support" />

    <div class="h-screen flex flex-col p-4 sm:p-6">
      <!-- Header -->
      <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Chat Support</h1>
        <Button variant="destructive" @click="clearChat" :disabled="messages.length === 0">
          Clear Chat
        </Button>
      </div>

      <!-- Chat Container -->
      <Card class="flex-1 flex flex-col rounded-2xl shadow">
        <!-- Messages -->
        <ScrollArea class="flex-1 p-4" ref="messagesContainer">
          <div v-for="message in messages" :key="message.id" class="flex mb-3"
            :class="message.sender === 'user' ? 'justify-end' : 'justify-start'">
            <div class="rounded-2xl px-4 py-2 w-full sm:max-w-md" :class="message.sender === 'user'
              ? 'bg-blue-600 text-white rounded-br-md'
              : 'bg-muted text-gray-900 dark:text-white rounded-bl-md'">
              <p class="text-sm" v-html="message.message.replace(/\n/g, '<br>')"></p>
              <p class="text-xs opacity-70 mt-1">{{ formatTime(message.created_at) }}</p>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="flex justify-start">
            <div class="bg-muted px-4 py-2 rounded-2xl w-fit">
              <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-100"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-200"></div>
              </div>
            </div>
          </div>
        </ScrollArea>

        <!-- Input -->
        <div class="border-t p-4">
          <form @submit.prevent="sendMessage" class="flex space-x-2">
            <Input v-model="newMessage" placeholder="Type your choice (1, 2, or 3)..." class="flex-1" />
            <Button type="submit" :disabled="!newMessage.trim()">Send</Button>
          </form>
        </div>
      </Card>
    </div>
  </AppLayout>
</template>
