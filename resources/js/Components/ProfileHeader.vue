<template>
  <div>
    <!-- Avatar + name -->
    <div class="flex flex-col items-center text-center mb-4">
      <img :src="user.avatar_url" :alt="user.name"
           class="w-20 h-20 rounded-full object-cover mb-3 ring-4 ring-stone-100">
      <h1 class="font-serif text-xl font-semibold text-stone-900 leading-tight">{{ user.name }}</h1>
      <p class="text-stone-500 text-sm">@{{ user.username }}</p>
    </div>

    <!-- Bio -->
    <p v-if="user.bio" class="text-stone-600 text-sm leading-relaxed text-center mb-4">{{ user.bio }}</p>

    <!-- Meta -->
    <div class="space-y-1.5 mb-4">
      <div v-if="user.location" class="flex items-center gap-2 text-xs text-stone-500">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ user.location }}
      </div>
      <div v-if="user.website" class="flex items-center gap-2 text-xs">
        <svg class="w-3.5 h-3.5 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        <a :href="user.website" target="_blank" rel="noopener"
           class="text-stone-600 hover:text-stone-900 hover:underline truncate">
          {{ user.website.replace(/^https?:\/\//, '') }}
        </a>
      </div>
    </div>

    <!-- Stats -->
    <div class="flex justify-center gap-6 py-3 border-y border-stone-100 mb-4">
      <div class="text-center">
        <p class="font-semibold text-stone-900 text-sm">{{ user.posts_count }}</p>
        <p class="text-xs text-stone-500">Posts</p>
      </div>
      <div class="text-center">
        <p class="font-semibold text-stone-900 text-sm">{{ user.followers_count }}</p>
        <p class="text-xs text-stone-500">Followers</p>
      </div>
      <div class="text-center">
        <p class="font-semibold text-stone-900 text-sm">{{ user.following_count }}</p>
        <p class="text-xs text-stone-500">Following</p>
      </div>
    </div>

    <!-- Action button -->
    <div class="flex gap-2">
      <Link v-if="isOwn" :href="route('profile.edit')"
            class="flex-1 text-center text-sm font-medium border border-stone-300 text-stone-700 px-4 py-2 rounded-full hover:bg-stone-50 transition-colors">
        Edit profile
      </Link>
      <button
        v-else-if="$page.props.auth.user"
        @click="$emit('toggleFollow')"
        :class="isFollowing
          ? 'border border-stone-300 text-stone-700 hover:border-red-300 hover:text-red-600 hover:bg-red-50'
          : 'bg-stone-900 text-white hover:bg-stone-700'"
        class="flex-1 text-sm font-medium px-4 py-2 rounded-full transition-all"
      >
        {{ isFollowing ? 'Following' : 'Follow' }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  user:        { type: Object,  required: true },
  isFollowing: { type: Boolean, default: false },
  isOwn:       { type: Boolean, default: false },
});
defineEmits(['toggleFollow']);
</script>
