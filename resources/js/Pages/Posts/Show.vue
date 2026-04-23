<template>
  <Head :title="`Post by @${post.user.username}`" />
  <AppLayout>
    <div class="max-w-2xl mx-auto px-4 py-8">

      <!-- Back -->
      <button @click="$inertia.visit(history.length > 1 ? 'javascript:history.back()' : route('feed'))"
              @click.prevent="goBack"
              class="flex items-center gap-2 text-sm text-stone-500 hover:text-stone-800 mb-6 transition-colors group">
        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
      </button>

      <!-- Post -->
      <article class="bg-white border border-stone-200 rounded-2xl p-6 mb-6">

        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <Link :href="route('profile.show', post.user.username)">
              <img :src="post.user.avatar_url" :alt="post.user.name"
                   class="w-12 h-12 rounded-full object-cover hover:opacity-90 transition-opacity">
            </Link>
            <div>
              <Link :href="route('profile.show', post.user.username)"
                    class="font-medium text-stone-900 hover:underline block">
                {{ post.user.name }}
              </Link>
              <div class="text-stone-400 text-sm flex items-center gap-1">
                <Link :href="route('profile.show', post.user.username)" class="hover:text-stone-600">
                  @{{ post.user.username }}
                </Link>
                <span>·</span>
                <time :datetime="post.created_at" :title="formatDate(post.created_at)">
                  {{ formatDate(post.created_at) }}
                </time>
              </div>
            </div>
          </div>

          <!-- Delete -->
          <div v-if="authUser && authUser.id === post.user_id" class="relative" ref="menuRef">
            <button @click="menuOpen = !menuOpen"
                    class="p-1.5 text-stone-400 hover:text-stone-600 rounded-md hover:bg-stone-100 transition-colors">
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
        <p class="text-stone-800 leading-relaxed whitespace-pre-wrap text-base mb-4">{{ post.body }}</p>

        <!-- Attachments -->
        <div v-if="post.attachments?.length" class="mb-4"
             :class="post.attachments.length > 1 ? 'grid grid-cols-2 gap-2' : ''">
          <template v-for="att in post.attachments" :key="att.id">
            <a v-if="att.type === 'image'" :href="att.url" target="_blank">
              <img :src="att.url" :alt="att.filename"
                   class="w-full rounded-xl object-cover max-h-96 hover:opacity-95 transition-opacity border border-stone-100">
            </a>
            <video v-else-if="att.type === 'video'" controls
                   class="w-full rounded-xl max-h-96 bg-black border border-stone-100">
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

        <!-- Like bar -->
        <div class="flex items-center gap-4 pt-4 border-t border-stone-100">
          <button
            v-if="authUser"
            @click="toggleLike"
            :class="localLiked ? 'text-rose-500' : 'text-stone-400 hover:text-rose-400'"
            class="flex items-center gap-2 text-sm transition-colors font-medium"
          >
            <svg class="w-5 h-5" :fill="localLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            {{ localLikeCount }} {{ localLikeCount === 1 ? 'like' : 'likes' }}
          </button>
          <span v-else class="text-sm text-stone-400">{{ post.likes_count }} likes</span>
          <span class="text-sm text-stone-400">{{ post.comments_count }} comments</span>
        </div>
      </article>

      <!-- Comments -->
      <div class="bg-white border border-stone-200 rounded-2xl p-6">
        <h2 class="font-serif text-lg font-semibold text-stone-900 mb-5">
          Comments
          <span class="text-stone-400 font-normal text-base ml-1">({{ localComments.length }})</span>
        </h2>

        <!-- Add comment -->
        <form v-if="authUser" @submit.prevent="submitComment" class="flex gap-3 mb-6">
          <img :src="authUser.avatar_url" class="w-8 h-8 rounded-full object-cover shrink-0 mt-0.5">
          <div class="flex-1 flex gap-2">
            <input
              v-model="commentBody"
              type="text"
              placeholder="Write a comment…"
              maxlength="1000"
              class="flex-1 bg-stone-50 border border-stone-200 rounded-full px-4 py-2 text-sm text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-400 focus:bg-white transition-colors"
            >
            <button
              type="submit"
              :disabled="!commentBody.trim() || commenting"
              class="bg-stone-800 text-white text-sm font-medium px-5 py-2 rounded-full hover:bg-stone-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all shrink-0"
            >Post</button>
          </div>
        </form>

        <!-- Comment list -->
        <div class="space-y-4">
          <CommentItem
            v-for="comment in localComments"
            :key="comment.id"
            :comment="comment"
            :post-id="post.id"
            @deleted="removeComment"
          />
          <p v-if="!localComments.length" class="text-stone-400 text-sm italic text-center py-6">
            No comments yet.
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTimeAgo } from '@/composables/useTimeAgo.js';
import CommentItem from '@/Components/CommentItem.vue';

const props = defineProps({
  post: { type: Object, required: true },
});

const page = usePage();
const authUser = page.props.auth.user;

const localLiked = ref(props.post.liked_by_auth);
const localLikeCount = ref(props.post.likes_count);
const localComments = ref([...(props.post.comments ?? [])]);
const commentBody = ref('');
const commenting = ref(false);
const menuOpen = ref(false);
const menuRef = ref(null);

async function toggleLike() {
  const { data } = await axios.post(route('posts.like', props.post.id));
  localLiked.value = data.liked;
  localLikeCount.value = data.count;
}

function deletePost() {
  if (!confirm('Delete this post?')) return;
  router.delete(route('posts.destroy', props.post.id));
}

async function submitComment() {
  if (!commentBody.value.trim()) return;
  commenting.value = true;
  try {
    const { data } = await axios.post(route('comments.store', props.post.id), { body: commentBody.value });
    localComments.value.push(data.comment);
    commentBody.value = '';
  } finally {
    commenting.value = false;
  }
}

function removeComment(id) {
  localComments.value = localComments.value.filter(c => c.id !== id);
}

function goBack() {
  if (window.history.length > 1) window.history.back();
  else router.visit(route('feed'));
}

const { formatFull: formatDate, humanSize } = useTimeAgo();

function handleOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) menuOpen.value = false;
}
onMounted(() => document.addEventListener('click', handleOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleOutside));
</script>
