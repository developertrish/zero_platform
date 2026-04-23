<template>
  <div class="space-y-3">

    <!-- Comments list -->
    <template v-if="localComments.length">
      <CommentItem
        v-for="comment in localComments"
        :key="comment.id"
        :comment="comment"
        :post-id="postId"
        @deleted="removeComment"
      />
    </template>
    <p v-else class="text-stone-400 text-sm italic">No comments yet. Be the first.</p>

    <!-- Add comment -->
    <form v-if="authUser" @submit.prevent="submit" class="flex gap-2 pt-1">
      <img :src="authUser.avatar_url" class="w-7 h-7 rounded-full object-cover shrink-0 mt-0.5">
      <div class="flex-1 flex gap-2">
        <input
          v-model="body"
          type="text"
          placeholder="Add a comment…"
          maxlength="1000"
          class="flex-1 bg-stone-50 border border-stone-200 rounded-full px-4 py-1.5 text-sm text-stone-800 placeholder-stone-400 focus:outline-none focus:border-stone-400 focus:bg-white transition-colors"
        >
        <button
          type="submit"
          :disabled="!body.trim() || loading"
          class="bg-stone-800 text-white text-xs font-medium px-4 py-1.5 rounded-full hover:bg-stone-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all shrink-0"
        >Post</button>
      </div>
    </form>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import CommentItem from '@/Components/CommentItem.vue';

const props = defineProps({
  postId: { type: Number, required: true },
  comments: { type: Array, default: () => [] },
});

const page = usePage();
const authUser = page.props.auth.user;

const localComments = ref([...props.comments]);
const body = ref('');
const loading = ref(false);

async function submit() {
  if (!body.value.trim()) return;
  loading.value = true;
  try {
    const { data } = await axios.post(route('comments.store', props.postId), { body: body.value });
    localComments.value.push(data.comment);
    body.value = '';
  } finally {
    loading.value = false;
  }
}

function removeComment(id) {
  localComments.value = localComments.value.filter(c => c.id !== id);
}
</script>
