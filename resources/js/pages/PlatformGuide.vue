<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import AppLogo from '@/components/AppLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Gavel, Users, Lock, Shield, BookOpen } from 'lucide-vue-next';
</script>

<template>
  <div class="min-h-screen bg-neutral-900 text-neutral-100 p-8">
    <!-- Header with Logo + Back -->
    <div class="flex justify-between items-center mb-10 animate-fade-slide">
      <div class="flex items-center gap-3">
        <AppLogo class="w-10 h-10" />
      </div>
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

    <!-- Title + Intro -->
    <h2 class="flex items-center justify-center gap-3 text-3xl font-bold text-center mb-6 animate-fade-slide">
      <BookOpen class="w-8 h-8 text-neutral-200" />
      Platform Guidelines
    </h2>
    <p class="max-w-3xl mx-auto text-neutral-400 text-center mb-12 leading-relaxed animate-fade-slide">
      Welcome to <span class="font-semibold text-neutral-100">PSITS Nexus</span>!  
      As your trusted platform, we’ve created these guidelines to ensure our space remains safe, inclusive, 
      and productive. By following these rules, we can all enjoy a better community together.
    </p>

    <!-- Guideline Sections -->
    <div class="grid gap-6 md:grid-cols-2 max-w-6xl mx-auto">
      <!-- Prohibited Content -->
      <Card class="guideline-card">
        <CardHeader>
          <CardTitle class="flex items-center gap-2 text-red-400">
            <Gavel class="w-5 h-5" />
            Prohibited Content & Behavior
          </CardTitle>
        </CardHeader>
        <CardContent class="text-neutral-300 leading-relaxed">
          <p class="mb-3">
            We want PSITS Nexus to be a safe environment. That’s why we strictly prohibit the following:
          </p>
          <ul class="list-disc pl-5 space-y-2">
            <li><b>Hate speech:</b> Content that promotes violence or discrimination against individuals or groups based on race, ethnicity, religion, gender, sexual orientation, disability, etc.</li>
            <li><b>Harassment & bullying:</b> Behavior intended to intimidate, threaten, or humiliate others, including doxing (sharing private information without consent).</li>
            <li><b>Violence & threats:</b> Any content that glorifies or incites violence, terrorism, or self-harm.</li>
            <li><b>Illegal activities:</b> Content promoting or discussing illegal acts, such as selling illicit drugs or firearms, or engaging in fraud.</li>
            <li><b>Spam & scams:</b> Repeated, unsolicited messages, or any fraudulent content designed to deceive users.</li>
            <li><b>Explicit content:</b> Material that is sexually explicit, graphic, or otherwise not safe for work (NSFW).</li>
          </ul>
        </CardContent>
      </Card>

      <!-- UGC Rules -->
      <Card class="guideline-card">
        <CardHeader>
          <CardTitle class="flex items-center gap-2 text-green-400">
            <Users class="w-5 h-5" />
            User-Generated Content Rules
          </CardTitle>
        </CardHeader>
        <CardContent class="text-neutral-300 leading-relaxed">
          <p class="mb-3">
            Since our platform relies on your contributions, we set clear rules for sharing content:
          </p>
          <ul class="list-disc pl-5 space-y-2">
            <li><b>Intellectual property:</b> Users must respect copyright and trademark laws. This means not posting content they don't own or have permission to use.</li>
            <li><b>Authenticity:</b> We encourage honesty. Don’t misrepresent your identity — some cases may even require real names.</li>
            <li><b>Relevance:</b> Content should always connect with the platform’s purpose. Off-topic or unrelated posts may be removed.</li>
          </ul>
        </CardContent>
      </Card>

      <!-- Safety & Privacy -->
      <Card class="guideline-card">
        <CardHeader>
          <CardTitle class="flex items-center gap-2 text-yellow-400">
            <Lock class="w-5 h-5" />
            Safety & Privacy
          </CardTitle>
        </CardHeader>
        <CardContent class="text-neutral-300 leading-relaxed">
          <p class="mb-3">
            Protecting your safety and privacy is one of our top responsibilities:
          </p>
          <ul class="list-disc pl-5 space-y-2">
            <li><b>Protection of personal information:</b> Don’t share private details such as addresses, phone numbers, or financial data.</li>
            <li><b>Minors’ safety:</b> We have zero tolerance for child exploitation. We also enforce age-appropriate protections.</li>
            <li><b>Account security:</b> Keep your account safe with strong passwords and two-factor authentication.</li>
          </ul>
        </CardContent>
      </Card>

      <!-- Enforcement -->
      <Card class="guideline-card">
        <CardHeader>
          <CardTitle class="flex items-center gap-2 text-blue-400">
            <Shield class="w-5 h-5" />
            Enforcement & Consequences
          </CardTitle>
        </CardHeader>
        <CardContent class="text-neutral-300 leading-relaxed">
          <p class="mb-3">
            To keep things fair, here’s how we enforce our rules:
          </p>
          <ul class="list-disc pl-5 space-y-2">
            <li><b>Reporting violations:</b> You can report content or behavior that breaks our guidelines.</li>
            <li><b>Disciplinary actions:</b> Depending on the severity, consequences may include warnings, content removal, temporary suspensions, or permanent bans.</li>
            <li><b>Appeals process:</b> If you feel a decision was unfair, you have the right to appeal.</li>
          </ul>
        </CardContent>
      </Card>
    </div>

    <!-- Closing -->
    <div class="max-w-4xl mx-auto mt-12 text-center text-neutral-400 text-sm leading-relaxed animate-fade-slide">
      These guidelines are not just rules, but our promise to you:  
      <span class="font-semibold text-neutral-100">we commit to building a respectful, safe, and inclusive 
      space for every PSITS member.</span>
    </div>
  </div>
</template>

<style scoped>
/* Fade-in + slight upward motion */
@keyframes fadeSlide {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-slide {
  animation: fadeSlide 0.8s ease forwards;
}

/* Card animations */
.guideline-card {
  background-color: #1f1f1f;
  border: 1px solid #2d2d2d;
  border-radius: 0.75rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.4);
  opacity: 0;
  transform: translateY(20px);
  animation: fadeSlide 0.8s ease forwards;
}
.guideline-card:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 16px rgba(0,0,0,0.6);
  transition: all 0.3s ease;
}
.guideline-card:nth-child(1) { animation-delay: 0.2s; }
.guideline-card:nth-child(2) { animation-delay: 0.4s; }
.guideline-card:nth-child(3) { animation-delay: 0.6s; }
.guideline-card:nth-child(4) { animation-delay: 0.8s; }
</style>
