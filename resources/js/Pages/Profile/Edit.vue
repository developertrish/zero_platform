<template>
  <Head title="Edit Profile" />
  <AppLayout>
    <div class="max-w-2xl mx-auto px-4 py-8">

      <div class="mb-8">
        <h1 class="font-serif text-2xl font-semibold text-stone-900">Edit profile</h1>
        <p class="text-stone-500 text-sm mt-1">Update your public profile information.</p>
      </div>

      <div class="bg-white border border-stone-200 rounded-2xl p-6 space-y-6">

        <!-- Avatar -->
        <div class="flex items-center gap-5">
          <img :src="avatarPreview || user.avatar_url" :alt="user.name"
               class="w-16 h-16 rounded-full object-cover ring-2 ring-stone-100">
          <div>
            <label class="cursor-pointer inline-flex items-center gap-2 text-sm font-medium text-stone-700 border border-stone-300 px-4 py-2 rounded-full hover:bg-stone-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Change photo
              <input type="file" class="sr-only" accept="image/*" @change="handleAvatar">
            </label>
            <p class="text-xs text-stone-400 mt-1">JPG, PNG or WebP · Max 2MB</p>
            <p v-if="form.errors.avatar" class="text-red-500 text-xs mt-1">{{ form.errors.avatar }}</p>
          </div>
        </div>

        <div class="border-t border-stone-100"></div>

        <!-- Name -->
        <div>
          <label class="block text-sm font-medium text-stone-700 mb-1.5">Display name</label>
          <input v-model="form.name" type="text" maxlength="100"
                 class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:border-stone-500 transition-colors">
          <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
        </div>

        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-stone-700 mb-1.5">Username</label>
          <div class="flex items-center border border-stone-300 rounded-xl px-4 py-2.5 focus-within:border-stone-500 transition-colors">
            <span class="text-stone-400 text-sm mr-1">@</span>
            <input v-model="form.username" type="text" maxlength="30"
                   class="flex-1 text-sm text-stone-900 focus:outline-none bg-transparent">
          </div>
          <p class="text-xs text-stone-400 mt-1">Letters, numbers, and underscores only.</p>
          <p v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</p>
        </div>

        <!-- Bio -->
        <div>
          <label class="block text-sm font-medium text-stone-700 mb-1.5">Bio</label>
          <textarea v-model="form.bio" rows="3" maxlength="300" placeholder="Tell the world a little about yourself…"
                    class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:border-stone-500 transition-colors resize-none"></textarea>
          <div class="flex justify-between">
            <p v-if="form.errors.bio" class="text-red-500 text-xs mt-1">{{ form.errors.bio }}</p>
            <p class="text-xs text-stone-400 mt-1 ml-auto">{{ form.bio?.length ?? 0 }}/300</p>
          </div>
        </div>

        <!-- Location -->
        <div>
          <label class="block text-sm font-medium text-stone-700 mb-1.5">Location</label>
          <input v-model="form.location" type="text" maxlength="100" placeholder="e.g. New York, USA"
                 class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:border-stone-500 transition-colors">
          <p v-if="form.errors.location" class="text-red-500 text-xs mt-1">{{ form.errors.location }}</p>
        </div>

        <!-- Website -->
        <div>
          <label class="block text-sm font-medium text-stone-700 mb-1.5">Website</label>
          <input v-model="form.website" type="url" placeholder="https://yoursite.com"
                 class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:border-stone-500 transition-colors">
          <p v-if="form.errors.website" class="text-red-500 text-xs mt-1">{{ form.errors.website }}</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-2 border-t border-stone-100">
          <Link :href="route('profile.show', user.username)"
                class="text-sm text-stone-500 hover:text-stone-800 transition-colors">
            Cancel
          </Link>
          <button
            @click="submit"
            :disabled="form.processing"
            class="bg-stone-900 text-white text-sm font-medium px-6 py-2.5 rounded-full hover:bg-stone-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
          >
            {{ form.processing ? 'Saving…' : 'Save changes' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user: { type: Object, required: true },
});

const avatarPreview = ref(null);

const form = useForm({
  name:     props.user.name,
  username: props.user.username,
  bio:      props.user.bio ?? '',
  location: props.user.location ?? '',
  website:  props.user.website ?? '',
  avatar:   null,
});

function handleAvatar(e) {
  const file = e.target.files[0];
  if (!file) return;
  form.avatar = file;
  avatarPreview.value = URL.createObjectURL(file);
}

function submit() {
  // When uploading files, browsers can't send PATCH natively.
  // We POST with a `_method=PATCH` field — Laravel's method spoofing handles it.
  form.transform(data => ({
    ...data,
    _method: 'PATCH',
  })).post(route('profile.update'), {
    forceFormData: true,
  });
}
</script>
