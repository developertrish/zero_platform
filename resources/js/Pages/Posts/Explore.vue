<template>
  <Head title="Explore" />
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8 flex gap-8">

      <!-- Feed column -->
      <div class="flex-1 min-w-0 space-y-4">
        <div class="flex items-center justify-between mb-2">
          <h1 class="font-serif text-2xl font-semibold text-stone-900">Explore</h1>
          <span class="text-xs text-stone-400 bg-stone-100 px-3 py-1 rounded-full">All posts · Newest first</span>
        </div>

        <PostCard v-for="post in posts.data" :key="post.id" :post="post" />

        <div v-if="!posts.data.length" class="text-center py-20">
          <div class="text-4xl mb-4">🌿</div>
          <p class="text-stone-500">Nothing here yet. Be the first to post.</p>
        </div>

        <!-- Pagination -->
        <Pagination :posts="posts" />
      </div>

      <!-- Sidebar -->
      <aside class="hidden lg:block w-64 shrink-0">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 sticky top-20">
          <h2 class="font-serif text-base font-semibold text-stone-900 mb-3">About Chronicle</h2>
          <p class="text-sm text-stone-500 leading-relaxed mb-4">
            A social platform where posts appear in the order they were written — oldest to newest. No ranking, no boosting, no algorithm.
          </p>
          <div class="space-y-2 text-sm text-stone-600">
            <div class="flex items-center gap-2">
              <span class="text-stone-400">📅</span> Chronological feed, always
            </div>
            <div class="flex items-center gap-2">
              <span class="text-stone-400">🚫</span> No engagement scoring
            </div>
            <div class="flex items-center gap-2">
              <span class="text-stone-400">📎</span> Images, videos & documents
            </div>
          </div>
        </div>
      </aside>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/PostCard.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  posts: { type: Object, required: true },
});
</script>
