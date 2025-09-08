<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types';
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ScrollArea } from '@/components/ui/scroll-area'
import { ref, nextTick } from 'vue'


const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Chatbot', href: '/ChatBot' } // or omit href if it's the current page
];


const messages = ref<{ id: number; sender: 'user' | 'bot'; message: string; created_at: Date }[]>([])
const newMessage = ref('')
const isTyping = ref(false)
const messagesContainer = ref<InstanceType<typeof ScrollArea> | null>(null)

const formatTime = (date: Date) =>
  new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })

const scrollToBottom = () => {
  nextTick(() => {
    const container = messagesContainer.value?.$el.querySelector('[data-radix-scroll-area-viewport]')
    if (container) container.scrollTop = container.scrollHeight
  })
}

const sendMessage = () => {
  if (!newMessage.value.trim()) return

  const userMessage = {
    id: Date.now(),
    sender: 'user' as const,
    message: newMessage.value,
    created_at: new Date(),
  }
  messages.value.push(userMessage)
  newMessage.value = ''
  scrollToBottom()

  // Show typing indicator
  isTyping.value = true

  setTimeout(() => {
    const botMessage = {
      id: Date.now() + 1,
      sender: 'bot' as const,
      message: "🤖 Thanks for your message! (Bot reply placeholder)",
      created_at: new Date(),
    }
    messages.value.push(botMessage)
    isTyping.value = false
    scrollToBottom()
  }, 1500)
}

const clearChat = () => {
  messages.value = []
}
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
          <div v-if="messages.length === 0" class="text-center text-gray-500 py-8">
            👋 Welcome! Start typing your question below.
          </div>

          <div v-for="message in messages" :key="message.id" class="flex mb-3"
            :class="message.sender === 'user' ? 'justify-end' : 'justify-start'">
            <div class="rounded-2xl px-4 py-2 w-full sm:max-w-md" :class="message.sender === 'user'
              ? 'bg-blue-600 text-white rounded-br-md'
              : 'bg-muted text-gray-900 dark:text-white rounded-bl-md'">
              <p class="text-sm">{{ message.message }}</p>
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
            <Input v-model="newMessage" placeholder="Type your message..." class="flex-1" />
            <Button type="submit" :disabled="!newMessage.trim()">Send</Button>
          </form>
        </div>
      </Card>
    </div>
  </AppLayout>
</template>
