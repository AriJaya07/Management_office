<script setup lang="ts">
definePageMeta({ layout: 'auth', middleware: 'guest' })

const auth = useAuthStore()
const { handle } = useApiError()

const state = reactive({ email: '', password: '' })
const loading = ref(false)

async function onSubmit() {
  loading.value = true
  try {
    await auth.login(state)
    navigateTo('/')
  } catch (error) {
    handle(error, 'Invalid credentials.')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <UCard>
    <template #header>
      <h1 class="text-xl font-semibold">Sign in</h1>
      <p class="text-sm text-gray-500">Office Management System</p>
    </template>

    <UForm :state="state" class="space-y-4" @submit="onSubmit">
      <UFormField label="Email" name="email" required>
        <UInput v-model="state.email" type="email" placeholder="you@office.com" class="w-full" />
      </UFormField>

      <UFormField label="Password" name="password" required>
        <UInput v-model="state.password" type="password" class="w-full" />
      </UFormField>

      <UButton type="submit" label="Sign in" block :loading="loading" />
    </UForm>

    <template #footer>
      <p class="text-sm text-center text-gray-500">
        No account?
        <NuxtLink to="/register" class="text-primary font-medium">Register</NuxtLink>
      </p>
    </template>
  </UCard>
</template>
