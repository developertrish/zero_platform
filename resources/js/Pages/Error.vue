<template>
  <Head :title="title" />

  <div class="min-h-screen bg-stone-50 flex flex-col items-center justify-center px-4 text-center">

    <!-- Decorative serif number -->
    <div class="font-serif text-[8rem] leading-none font-semibold text-stone-200 select-none mb-2">
      {{ status }}
    </div>

    <h1 class="font-serif text-2xl font-semibold text-stone-800 mb-3">{{ title }}</h1>
    <p class="text-stone-500 text-sm max-w-sm leading-relaxed mb-8">{{ description }}</p>

    <div class="flex items-center gap-3">
      <button
        @click="goBack"
        class="text-sm text-stone-600 border border-stone-300 px-5 py-2.5 rounded-full hover:bg-stone-100 transition-colors"
      >
        ← Go back
      </button>
      <Link
        :href="route('feed')"
        class="text-sm bg-stone-900 text-white px-5 py-2.5 rounded-full hover:bg-stone-700 transition-colors"
      >
        Home
      </Link>
    </div>

    <!-- Subtle branding -->
    <div class="mt-16 font-serif text-stone-300 text-lg">Chronicle</div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  status: { type: Number, required: true },
});

const title = computed(() => ({
  503: 'Service Unavailable',
  500: 'Server Error',
  404: 'Page Not Found',
  403: 'Forbidden',
}[props.status] ?? 'An Error Occurred'));

const description = computed(() => ({
  503: "We're doing some maintenance. Check back soon.",
  500: 'Something went wrong on our end. We\'ve been notified.',
  404: 'The page you\'re looking for doesn\'t exist or has been moved.',
  403: 'You don\'t have permission to access this page.',
}[props.status] ?? 'An unexpected error occurred.'));

function goBack() {
  if (window.history.length > 1) window.history.back();
  else router.visit(route('feed'));
}
</script>
