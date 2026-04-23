<template>
  <article class="bg-white border border-stone-200 rounded-2xl p-5 hover:border-stone-300 transition-colors">

    <!-- Header -->
    <div class="flex items-start justify-between mb-3">
      <div class="flex items-center gap-3">
        <Link :href="route('profile.show', post.user.username)">
          <img :src="post.user.avatar_url" :alt="post.user.name"
               class="w-10 h-10 rounded-full object-cover hover:opacity-90 transition-opacity">
        </Link>
        <div>
          <Link :href="route('profile.show', post.user.username)"
                class="font-medium text-stone-900 hover:underline text-sm leading-tight block">
            {{ post.user.name }}
          </Link>
          <div class="text-stone-400 text-xs flex items-center gap-1">
            <Link :href="route('profile.show', post.user.username)" class="hover:text-stone-600">
              @{{ post.user.username }}
            </Link>
            <span>·</span>
            <time :datetime="post.created_at" :title="formatDate(post.created_at)">
              {{ timeAgo(post.created_at) }}
            </time>
          </div>
        </div>
      </div>

      <!-- Delete (own posts only) -->
      <div v-if="authUser && authUser.id === post.user_id" class="relative" ref="menuRef">
        <button @click="menuOpen = !menuOpen" class="p-1 text-stone-400 hover:text-stone-600 rounded-md hover:bg-stone-100 transition-colors">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
          </svg>
        </button>
        <div v-if="menuOpen" class="absolute right-0 mt-1 w-36 bg-white border border-stone-200 rounded-xl shadow-lg py-1 text-sm z-10">
          <button @click="deletePost" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
            Delete post
          </button>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="text-stone-800 text-sm leading-relaxed whitespace-pre-wrap mb-3">{{ post.body }}</div>

    <!-- Attachments -->
    <div v-if="post.attachments?.length" class="mb-3" :class="post.attachments.length > 1 ? 'grid grid-cols-2 gap-2' : ''">
      <template v-for="att in post.attachments" :key="att.id">
        <a v-if="att.type === 'image'" :href="att.url" target="_blank">
          <img :src="att.url" :alt="att.filename"
               class="w-full rounded-xl object-cover max-h-80 hover:opacity-95 transition-opacity border border-stone-100">
        </a>
        <video v-else-if="att.type === 'video'" controls
               class="w-full rounded-xl max-h-80 bg-black border border-stone-100">
          <source :src="att.url" :type="att.mime_type">
        </video>
        <a v-else :href="att.url" target="_blank"
           class="flex items-center gap-3 p-3 bg-stone-50 border border-stone-200 rounded-xl hover:bg-stone-100 transition-colors">
          <div class="w-8 h-8 bg-stone-200 rounded-lg flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div class="min-w-0">
            <p class="text-sm font-medium text-stone-800 truncate">{{ att.filename }}</p>
            <p class="text-xs text-stone-400">{{ humanSize(att.size) }}</p>
          </div>
        </a>
      </template>
    </div>

    <!-- Actions bar -->
    <div class="flex items-center gap-4 pt-2 border-t border-stone-100">

      <!-- Like -->
      <button
        v-if="authUser"
        @click="toggleLike"
        :class="localLiked ? 'text-rose-500' : 'text-stone-400 hover:text-rose-400'"
        class="flex items-center gap-1.5 text-sm transition-colors"
      >
        <svg class="w-4 h-4" :fill="localLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span v-if="localLikeCount > 0">{{ localLikeCount }}</span>
      </button>
      <span v-else class="flex items-center gap-1.5 text-sm text-stone-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span v-if="post.likes_count > 0">{{ post.likes_count }}</span>
      </span>

      <!-- Comments toggle -->
      <button @click="showComments = !showComments"
              class="flex items-center gap-1.5 text-sm text-stone-400 hover:text-stone-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span v-if="post.comments_count > 0">{{ post.comments_count }}</span>
      </button>

      <!-- Permalink -->
      <Link :href="route('posts.show', post.id)" class="ml-auto text-stone-300 hover:text-stone-500 transition-colors" title="View post">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
      </Link>
    </div>

    <!-- Comments section -->
    <Transition
      enter-active-class="transition ease-out duration-150"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
    >
      <div v-if="showComments" class="mt-4 pt-3 border-t border-stone-100">
        <CommentThread :post-id="post.id" :comments="post.comments" />
      </div>
    </Transition>

  </article>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useTimeAgo } from '@/composables/useTimeAgo.js';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import CommentThread from '@/Components/CommentThread.vue';

const props = defineProps({
  post: { type: Object, required: true },
});

const page = usePage();
const authUser = page.props.auth.user;

const showComments = ref(false);
const menuOpen = ref(false);
const menuRef = ref(null);

const localLiked = ref(props.post.liked_by_auth);
const localLikeCount = ref(props.post.likes_count);

async function toggleLike() {
  try {
    const { data } = await axios.post(route('posts.like', props.post.id));
    localLiked.value = data.liked;
    localLikeCount.value = data.count;
  } catch (e) { /* noop */ }
}

function deletePost() {
  if (!confirm('Delete this post?')) return;
  router.delete(route('posts.destroy', props.post.id));
}

const { timeAgo, formatFull: formatDate, humanSize } = useTimeAgo();

// Close menu on outside click
function handleOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) menuOpen.value = false;
}
onMounted(() => document.addEventListener('click', handleOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleOutside));
</script>
