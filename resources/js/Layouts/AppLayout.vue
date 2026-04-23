<template>
  <div class="min-h-screen flex flex-col">

    <!-- Flash toast -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="toast" class="fixed top-4 right-4 z-50 max-w-sm">
        <div
          :class="toast.type === 'error' ? 'bg-red-700' : 'bg-stone-800'"
          class="text-white px-5 py-3 rounded-xl shadow-lg text-sm flex items-center gap-3"
        >
          <span>{{ toast.message }}</span>
          <button @click="toast = null" class="ml-auto opacity-60 hover:opacity-100 leading-none">✕</button>
        </div>
      </div>
    </Transition>

    <!-- Top Nav -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-sm border-b border-stone-200">
      <div class="max-w-4xl mx-auto px-4 flex items-center h-14 gap-6">

        <Link :href="route('feed')" class="font-serif text-xl font-semibold text-stone-900 tracking-tight shrink-0">
          Chronicle
        </Link>

        <nav v-if="$page.props.auth.user" class="flex items-center gap-1 text-sm">
          <Link
            :href="route('feed')"
            :class="[$page.url === '/' ? 'bg-stone-100 text-stone-900' : 'text-stone-500 hover:text-stone-800 hover:bg-stone-50']"
            class="px-3 py-1.5 rounded-md font-medium transition-colors"
          >Feed</Link>
          <Link
            :href="route('explore')"
            :class="[$page.url.startsWith('/explore') ? 'bg-stone-100 text-stone-900' : 'text-stone-500 hover:text-stone-800 hover:bg-stone-50']"
            class="px-3 py-1.5 rounded-md font-medium transition-colors"
          >Explore</Link>
        </nav>

        <div class="ml-auto flex items-center gap-3">
          <template v-if="$page.props.auth.user">
            <!-- New Post button -->
            <button
              @click="composeOpen = true"
              class="hidden sm:inline-flex items-center gap-1.5 bg-stone-900 text-white text-sm font-medium px-4 py-1.5 rounded-full hover:bg-stone-700 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
              </svg>
              New post
            </button>

            <!-- Avatar dropdown -->
            <div class="relative" ref="dropdownRef">
              <button @click="dropdownOpen = !dropdownOpen" class="flex items-center">
                <img
                  :src="$page.props.auth.user.avatar_url"
                  :alt="$page.props.auth.user.name"
                  class="w-8 h-8 rounded-full object-cover ring-2 ring-transparent hover:ring-stone-300 transition-all"
                >
              </button>
              <Transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div
                  v-if="dropdownOpen"
                  class="absolute right-0 mt-2 w-44 bg-white border border-stone-200 rounded-xl shadow-lg py-1 text-sm z-50"
                >
                  <Link
                    :href="route('profile.show', $page.props.auth.user.username)"
                    class="block px-4 py-2 text-stone-700 hover:bg-stone-50"
                    @click="dropdownOpen = false"
                  >@{{ $page.props.auth.user.username }}</Link>
                  <Link
                    :href="route('profile.edit')"
                    class="block px-4 py-2 text-stone-700 hover:bg-stone-50"
                    @click="dropdownOpen = false"
                  >Edit profile</Link>
                  <div class="border-t border-stone-100 my-1"></div>
                  <button
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50"
                  >Sign out</button>
                </div>
              </Transition>
            </div>
          </template>

          <template v-else>
            <Link :href="route('login')" class="text-stone-600 hover:text-stone-900 font-medium text-sm">Sign in</Link>
            <Link :href="route('register')" class="bg-stone-900 text-white px-4 py-1.5 rounded-full font-medium text-sm hover:bg-stone-700 transition-colors">Join</Link>
          </template>
        </div>
      </div>
    </header>

    <!-- Page content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Compose Modal -->
    <ComposeModal v-if="$page.props.auth.user" v-model:open="composeOpen" />

  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import ComposeModal from '@/Components/ComposeModal.vue';

const page = usePage();
const composeOpen = ref(false);
const dropdownOpen = ref(false);
const dropdownRef = ref(null);
const toast = ref(null);
let toastTimer = null;

// Watch for flash messages from Inertia
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success || flash?.error) {
      clearTimeout(toastTimer);
      toast.value = {
        type: flash.error ? 'error' : 'success',
        message: flash.success || flash.error,
      };
      toastTimer = setTimeout(() => { toast.value = null; }, 3500);
    }
  },
  { immediate: true, deep: true }
);

// Close dropdown on outside click
function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    dropdownOpen.value = false;
  }
}
onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));

function logout() {
  router.post(route('logout'));
}
</script>
