<template>
  <Head title="Feed" />
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8 flex gap-8">

      <!-- Feed column -->
      <div class="flex-1 min-w-0 space-y-4">
        <div class="flex items-center justify-between mb-2">
          <h1 class="font-serif text-2xl font-semibold text-stone-900">Your feed</h1>
          <span class="text-xs text-stone-400 bg-stone-100 px-3 py-1 rounded-full">Chronological · No algorithm</span>
        </div>

        <!-- Mobile compose -->
        <button
          @click="composeOpen = true"
          class="sm:hidden w-full flex items-center gap-3 bg-white border border-stone-200 rounded-2xl px-5 py-3 text-stone-400 hover:border-stone-300 transition-colors text-sm"
        >
          <img :src="$page.props.auth.user.avatar_url" class="w-8 h-8 rounded-full object-cover">
          What's on your mind?
        </button>

        <template v-if="posts.data.length">
          <PostCard v-for="post in posts.data" :key="post.id" :post="post" />

          <!-- Pagination -->
          <Pagination :posts="posts" />
        </template>

        <div v-else class="text-center py-20">
          <div class="text-4xl mb-4">📰</div>
          <h2 class="font-serif text-xl font-semibold text-stone-800 mb-2">Your feed is empty</h2>
          <p class="text-stone-500 text-sm mb-6">Follow some people or post something to get started.</p>
          <Link :href="route('explore')" class="bg-stone-900 text-white px-6 py-2.5 rounded-full text-sm font-medium hover:bg-stone-700 transition-colors">
            Explore posts
          </Link>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="hidden lg:block w-64 shrink-0 space-y-5">
        <!-- Who to follow -->
        <div v-if="suggestions.length" class="bg-white border border-stone-200 rounded-2xl p-5">
          <h2 class="font-serif text-base font-semibold text-stone-900 mb-4">Who to follow</h2>
          <div class="space-y-3">
            <div v-for="user in suggestions" :key="user.id" class="flex items-center gap-3">
              <Link :href="route('profile.show', user.username)">
                <img :src="user.avatar_url" class="w-9 h-9 rounded-full object-cover">
              </Link>
              <div class="flex-1 min-w-0">
                <Link :href="route('profile.show', user.username)" class="text-sm font-medium text-stone-800 hover:underline block truncate">
                  {{ user.name }}
                </Link>
                <p class="text-xs text-stone-400 truncate">@{{ user.username }}</p>
              </div>
              <form @submit.prevent="follow(user)">
                <button type="submit"
                        class="text-xs bg-stone-900 text-white px-3 py-1 rounded-full hover:bg-stone-700 transition-colors shrink-0">
                  Follow
                </button>
              </form>
            </div>
          </div>
        </div>

        <p class="text-xs text-stone-400 text-center px-2">
          Chronicle shows posts in the order they were written. No scores, no boosts, no ads.
        </p>
      </aside>
    </div>

    <ComposeModal v-model:open="composeOpen" />
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/PostCard.vue';
import ComposeModal from '@/Components/ComposeModal.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  posts:       { type: Object, required: true },
  suggestions: { type: Array,  default: () => [] },
});

const composeOpen = ref(false);

function follow(user) {
  router.post(route('users.follow', user.username), {}, { preserveScroll: true });
}
</script>
