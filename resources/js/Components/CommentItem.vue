<template>
  <div :class="depth > 0 ? 'ml-8 pl-3 border-l-2 border-stone-100' : ''">
    <div class="flex gap-2">
      <Link :href="route('profile.show', comment.user.username)" class="shrink-0">
        <img :src="comment.user.avatar_url" class="w-6 h-6 rounded-full object-cover">
      </Link>

      <div class="flex-1 min-w-0">
        <div class="bg-stone-50 rounded-xl px-3 py-2">
          <div class="flex items-baseline gap-2 mb-0.5">
            <Link :href="route('profile.show', comment.user.username)"
                  class="text-xs font-semibold text-stone-800 hover:underline">
              {{ comment.user.name }}
            </Link>
            <span class="text-xs text-stone-400">{{ timeAgo(comment.created_at) }}</span>
          </div>
          <p class="text-xs text-stone-700 leading-relaxed">{{ comment.body }}</p>
        </div>

        <!-- Actions -->
        <div v-if="authUser" class="flex items-center gap-3 mt-1 ml-1">
          <button
            @click="toggleLike"
            :class="localLiked ? 'text-rose-500' : 'text-stone-400 hover:text-rose-400'"
            class="text-xs flex items-center gap-1 transition-colors"
          >
            <svg class="w-3 h-3" :fill="localLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            {{ localLikeCount > 0 ? localLikeCount : 'Like' }}
          </button>

          <button v-if="depth === 0" @click="showReply = !showReply"
                  class="text-xs text-stone-400 hover:text-stone-600 transition-colors">
            Reply
          </button>

          <button
            v-if="authUser.id === comment.user_id"
            @click="deleteComment"
            class="text-xs text-stone-300 hover:text-red-500 transition-colors"
          >Delete</button>
        </div>

        <!-- Reply form -->
        <form v-if="authUser && depth === 0 && showReply" @submit.prevent="submitReply" class="flex gap-2 mt-2">
          <input
            v-model="replyBody"
            type="text"
            placeholder="Write a reply…"
            class="flex-1 bg-stone-50 border border-stone-200 rounded-full px-3 py-1 text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-400 transition-colors"
          >
          <button
            type="submit"
            :disabled="!replyBody.trim() || replyLoading"
            class="bg-stone-700 text-white text-xs px-3 py-1 rounded-full hover:bg-stone-600 disabled:opacity-40 transition-all shrink-0"
          >Reply</button>
        </form>

        <!-- Replies -->
        <div v-if="localReplies.length && depth === 0" class="mt-2 space-y-2">
          <CommentItem
            v-for="reply in localReplies"
            :key="reply.id"
            :comment="reply"
            :post-id="postId"
            :depth="1"
            @deleted="removeReply"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTimeAgo } from '@/composables/useTimeAgo.js';
import axios from 'axios';

const props = defineProps({
  comment: { type: Object, required: true },
  postId:  { type: Number, required: true },
  depth:   { type: Number, default: 0 },
});
const emit = defineEmits(['deleted']);

const page = usePage();
const authUser = page.props.auth.user;

const localLiked = ref(props.comment.liked_by_auth ?? false);
const localLikeCount = ref(props.comment.likes_count ?? 0);
const localReplies = ref([...(props.comment.replies ?? [])]);
const showReply = ref(false);
const replyBody = ref('');
const replyLoading = ref(false);

async function toggleLike() {
  try {
    const { data } = await axios.post(route('comments.like', props.comment.id));
    localLiked.value = data.liked;
    localLikeCount.value = data.count;
  } catch (e) { /* noop */ }
}

async function deleteComment() {
  if (!confirm('Delete this comment?')) return;
  await axios.delete(route('comments.destroy', props.comment.id));
  emit('deleted', props.comment.id);
}

async function submitReply() {
  if (!replyBody.value.trim()) return;
  replyLoading.value = true;
  try {
    const { data } = await axios.post(route('comments.store', props.postId), {
      body: replyBody.value,
      parent_id: props.comment.id,
    });
    localReplies.value.push(data.comment);
    replyBody.value = '';
    showReply.value = false;
  } finally {
    replyLoading.value = false;
  }
}

function removeReply(id) {
  localReplies.value = localReplies.value.filter(r => r.id !== id);
}

const { timeAgo } = useTimeAgo();
</script>
