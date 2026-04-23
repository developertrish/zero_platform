<template>
  <Head title="Create account" />
  <div class="min-h-screen bg-stone-50 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">

      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="font-serif text-3xl font-semibold text-stone-900">Chronicle</h1>
        <p class="text-stone-500 text-sm mt-2">No algorithm. Just people.</p>
      </div>

      <div class="bg-white border border-stone-200 rounded-2xl p-8 shadow-sm">
        <h2 class="font-serif text-xl font-semibold text-stone-900 mb-6">Create an account</h2>

        <form @submit.prevent="submit" class="space-y-4">

          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Full name</label>
            <input
              v-model="form.name"
              type="text"
              autocomplete="name"
              required
              class="w-full border rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none transition-colors"
              :class="form.errors.name ? 'border-red-400' : 'border-stone-300 focus:border-stone-500'"
            >
            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Username</label>
            <div
              class="flex items-center border rounded-xl px-4 py-2.5 transition-colors"
              :class="form.errors.username ? 'border-red-400' : 'border-stone-300 focus-within:border-stone-500'"
            >
              <span class="text-stone-400 text-sm mr-1">@</span>
              <input
                v-model="form.username"
                type="text"
                autocomplete="username"
                maxlength="30"
                required
                class="flex-1 text-sm text-stone-900 focus:outline-none bg-transparent"
              >
            </div>
            <p class="text-xs text-stone-400 mt-1">Letters, numbers, and underscores only.</p>
            <p v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              class="w-full border rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none transition-colors"
              :class="form.errors.email ? 'border-red-400' : 'border-stone-300 focus:border-stone-500'"
            >
            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Password</label>
            <input
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              required
              class="w-full border rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none transition-colors"
              :class="form.errors.password ? 'border-red-400' : 'border-stone-300 focus:border-stone-500'"
            >
            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Confirm password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:border-stone-500 transition-colors"
            >
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-stone-900 text-white text-sm font-medium py-2.5 rounded-full hover:bg-stone-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all mt-2"
          >
            {{ form.processing ? 'Creating account…' : 'Create account' }}
          </button>
        </form>
      </div>

      <p class="text-center text-sm text-stone-500 mt-6">
        Already have an account?
        <Link :href="route('login')" class="text-stone-900 font-medium hover:underline">Sign in</Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name:                  '',
  username:              '',
  email:                 '',
  password:              '',
  password_confirmation: '',
});

function submit() {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}
</script>
