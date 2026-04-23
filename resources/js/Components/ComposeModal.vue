<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="open" class="fixed inset-0 z-50 flex items-start justify-center pt-20 px-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="$emit('update:open', false)"></div>

        <!-- Modal -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
        >
          <div v-if="open" class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl p-6 z-10">
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-serif text-lg font-semibold text-stone-900">New post</h2>
              <button @click="$emit('update:open', false)" class="text-stone-400 hover:text-stone-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="submit">
              <div class="flex gap-3">
                <img :src="$page.props.auth.user.avatar_url" class="w-9 h-9 rounded-full object-cover shrink-0 mt-0.5">

                <div class="flex-1">
                  <textarea
                    v-model="form.body"
                    rows="4"
                    placeholder="What's on your mind?"
                    maxlength="5000"
                    class="w-full resize-none text-stone-900 placeholder-stone-400 text-base focus:outline-none bg-transparent"
                    required
                  ></textarea>

                  <!-- File previews -->
                  <div v-if="previews.length" class="flex flex-wrap gap-2 mt-2">
                    <div v-for="(p, i) in previews" :key="i" class="relative">
                      <img v-if="p.isImage" :src="p.url" class="w-16 h-16 object-cover rounded-lg border border-stone-200">
                      <div v-else class="w-16 h-16 bg-stone-100 rounded-lg border border-stone-200 flex items-center justify-center text-xs text-stone-500 text-center px-1 break-all">
                        {{ p.name }}
                      </div>
                      <button
                        type="button"
                        @click="removeFile(i)"
                        class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-stone-800 text-white rounded-full text-xs flex items-center justify-center"
                      >✕</button>
                    </div>
                  </div>

                  <!-- Error messages -->
                  <p v-if="form.errors.body" class="text-red-500 text-xs mt-1">{{ form.errors.body }}</p>
                  <p v-if="form.errors.attachments" class="text-red-500 text-xs mt-1">{{ form.errors.attachments }}</p>

                  <div class="flex items-center justify-between mt-3 pt-3 border-t border-stone-100">
                    <div class="flex items-center gap-3">
                      <label class="cursor-pointer text-stone-400 hover:text-stone-700 transition-colors" title="Attach files">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <input
                          type="file"
                          multiple
                          class="sr-only"
                          accept="image/*,video/*,.pdf,.doc,.docx,.zip"
                          @change="handleFiles"
                        >
                      </label>
                      <span class="text-xs text-stone-400">{{ form.body.length }}/5000</span>
                    </div>
                    <button
                      type="submit"
                      :disabled="form.body.trim().length === 0 || form.processing"
                      class="bg-stone-900 text-white text-sm font-medium px-5 py-1.5 rounded-full hover:bg-stone-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                    >
                      {{ form.processing ? 'Publishing…' : 'Publish' }}
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({ open: Boolean });
const emit = defineEmits(['update:open']);
const page = usePage();

const previews = ref([]);
const fileList = ref([]);

const form = useForm({
  body: '',
  attachments: [],
});

function handleFiles(e) {
  const files = Array.from(e.target.files);
  files.forEach(file => {
    fileList.value.push(file);
    if (file.type.startsWith('image/')) {
      const url = URL.createObjectURL(file);
      previews.value.push({ url, isImage: true, name: file.name });
    } else {
      previews.value.push({ url: null, isImage: false, name: file.name });
    }
  });
}

function removeFile(index) {
  previews.value.splice(index, 1);
  fileList.value.splice(index, 1);
}

function submit() {
  form.attachments = fileList.value;
  form.post(route('posts.store'), {
    forceFormData: true,
    onSuccess: () => {
      form.reset();
      previews.value = [];
      fileList.value = [];
      emit('update:open', false);
    },
  });
}

// Reset on close
watch(() => props.open, (val) => {
  if (!val) {
    form.reset();
    form.clearErrors();
    previews.value = [];
    fileList.value = [];
  }
});
</script>
