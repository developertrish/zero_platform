<template>
  <div v-if="links.prev || links.next" class="flex items-center justify-center gap-3 pt-6">
    <Link
      v-if="links.prev"
      :href="links.prev"
      preserve-scroll
      class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-stone-200 rounded-xl text-sm text-stone-600 hover:bg-stone-50 hover:border-stone-300 transition-colors"
    >
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      Newer
    </Link>

    <span class="text-xs text-stone-400">
      Page {{ meta.current_page }} of {{ meta.last_page }}
    </span>

    <Link
      v-if="links.next"
      :href="links.next"
      preserve-scroll
      class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-stone-200 rounded-xl text-sm text-stone-600 hover:bg-stone-50 hover:border-stone-300 transition-colors"
    >
      Older
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </Link>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  posts: { type: Object, required: true },
});

const links = computed(() => ({
  prev: props.posts.prev_page_url,
  next: props.posts.next_page_url,
}));

const meta = computed(() => ({
  current_page: props.posts.current_page,
  last_page:    props.posts.last_page,
}));
</script>
