<template>
  <Head title="Sign in" />
  <div class="min-h-screen bg-stone-50 flex items-center justify-center px-4">
    <div class="w-full max-w-sm">

      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="font-serif text-3xl font-semibold text-stone-900">Chronicle</h1>
        <p class="text-stone-500 text-sm mt-2">The feed that's just a feed.</p>
      </div>

      <div class="bg-white border border-stone-200 rounded-2xl p-8 shadow-sm">
        <h2 class="font-serif text-xl font-semibold text-stone-900 mb-6">Sign in</h2>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              class="w-full border rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none transition-colors"
              :class="form.errors.email ? 'border-red-400 focus:border-red-400' : 'border-stone-300 focus:border-stone-500'"
            >
            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-sm font-medium text-stone-700">Password</label>
            </div>
            <input
              v-model="form.password"
              type="password"
              autocomplete="current-password"
              required
              class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-stone-900 focus:outline-none focus:border-stone-500 transition-colors"
            >
            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
          </div>

          <div class="flex items-center gap-2">
            <input v-model="form.remember" id="remember" type="checkbox"
                   class="w-4 h-4 rounded border-stone-300 text-stone-900 focus:ring-stone-500">
            <label for="remember" class="text-sm text-stone-600">Remember me</label>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-stone-900 text-white text-sm font-medium py-2.5 rounded-full hover:bg-stone-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all mt-2"
          >
            {{ form.processing ? 'Signing in…' : 'Sign in' }}
          </button>
        </form>
      </div>

      <p class="text-center text-sm text-stone-500 mt-6">
        Don't have an account?
        <Link :href="route('register')" class="text-stone-900 font-medium hover:underline">Join Chronicle</Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  email:    '',
  password: '',
  remember: false,
});

function submit() {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
}
</script>
