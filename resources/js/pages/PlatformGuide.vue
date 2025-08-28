<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen } from 'lucide-vue-next';
</script>

<template>
  <div class="min-h-screen bg-neutral-900 text-neutral-100 p-8">
    <!-- Back to Dashboard button -->
    <div class="flex justify-end mb-6">
      <Link :href="route('dashboard')">
        <Button 
          variant="outline" 
          class="flex items-center gap-2 bg-neutral-800 hover:bg-neutral-700 text-neutral-100 border-neutral-700"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Dashboard</span>
        </Button>
      </Link>
    </div>

    <!-- Page Title with Icon -->
    <h1 class="flex items-center justify-center gap-3 text-3xl font-bold mb-8 text-neutral-100">
      <BookOpen class="w-8 h-8 text-neutral-200" />
      Platform Guidelines
    </h1>

    <!-- Card Content -->
    <Card class="max-w-4xl mx-auto bg-neutral-800 border border-neutral-700 shadow-lg">
      <CardHeader>
        <CardTitle class="text-xl text-neutral-100">Our Commitment to a Safe Community</CardTitle>
      </CardHeader>
      <CardContent class="space-y-6 leading-relaxed text-neutral-300">
        <p>
          At <span class="font-semibold text-neutral-100">PSITS Nexus</span>, we establish these platform guidelines 
          to ensure a safe, respectful, and productive space for all our members. These rules help us protect 
          our community and promote responsible engagement.
        </p>

        <h2 class="text-lg font-semibold text-neutral-100">1. Prohibited Content and Behavior</h2>
        <p>
          We strictly forbid hate speech, harassment, bullying, threats, violence, illegal activities, scams, spam, 
          and explicit content. Any content that promotes discrimination, intimidation, or harmful acts has no place 
          in PSITS Nexus.
        </p>

        <h2 class="text-lg font-semibold text-neutral-100">2. User-Generated Content (UGC)</h2>
        <p>
          We expect our members to respect intellectual property, be authentic, and stay relevant. Sharing content 
          you do not own or misrepresenting your identity is not allowed. All contributions should align with the 
          purpose of PSITS Nexus.
        </p>

        <h2 class="text-lg font-semibold text-neutral-100">3. Safety and Privacy</h2>
        <p>
          Protecting personal information is one of our top priorities. Members are not allowed to share sensitive 
          details such as addresses, phone numbers, or financial data. We are committed to ensuring minors’ safety, 
          and we encourage everyone to secure their accounts with strong passwords and two-factor authentication.
        </p>

        <h2 class="text-lg font-semibold text-neutral-100">4. Enforcement and Consequences</h2>
        <p>
          When violations occur, we take action. We provide reporting tools so that members can alert us of any 
          issues. Consequences may include warnings, content removal, suspension, or even permanent bans. An appeals 
          process is available to ensure fairness in every decision we make.
        </p>

        <p>
          By being part of PSITS Nexus, you agree to uphold these guidelines and help us foster an inclusive, 
          respectful, and empowering digital community.
        </p>
      </CardContent>
    </Card>
  </div>
</template>
