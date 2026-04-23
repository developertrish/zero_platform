<template>
  <Head :title="`@${user.username}`" />
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8 flex gap-8">

      <!-- Posts column -->
      <div class="flex-1 min-w-0 space-y-4">
        <!-- Mobile profile header -->
        <div class="lg:hidden bg-white border border-stone-200 rounded-2xl p-5 mb-2">
          <ProfileHeader :user="user" :is-following="localFollowing" :is-own="isOwn" @toggle-follow="toggleFollow" />
        </div>

        <h2 class="font-serif text-lg font-semibold text-stone-800 mb-1">Posts</h2>

        <PostCard v-for="post in posts.data" :key="post.id" :post="post" />

        <div v-if="!posts.data.length" class="text-center py-16 text-stone-400">
          <div class="text-4xl mb-3">✏️</div>
          <p class="text-sm">{{ isOwn ? "You haven't posted anything yet." : `${user.name} hasn't posted yet.` }}</p>
        </div>

        <!-- Pagination -->
        <Pagination :posts="posts" />
      </div>

      <!-- Sidebar profile card -->
      <aside class="hidden lg:block w-64 shrink-0">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 sticky top-20">
          <ProfileHeader :user="user" :is-following="localFollowing" :is-own="isOwn" @toggle-follow="toggleFollow" />
        </div>
      </aside>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/PostCard.vue';
import ProfileHeader from '@/Components/ProfileHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  user:        { type: Object,  required: true },
  posts:       { type: Object,  required: true },
  isFollowing: { type: Boolean, default: false },
  isOwn:       { type: Boolean, default: false },
});

const localFollowing = ref(props.isFollowing);

function toggleFollow() {
  router.post(route('users.follow', props.user.username), {}, {
    preserveScroll: true,
    onSuccess: () => { localFollowing.value = !localFollowing.value; },
  });
}
</script>
