<script setup lang="ts">

definePageMeta({ layout: 'auth', middleware: 'guest' })

const auth = useAuthStore()
const { handle } = useApiError()

const state = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const loading = ref(false)

async function onSubmit() {
    loading.value = true
    try {
        await auth.register(state)
        navigateTo('/')
    } catch (error) {
        handle(error, 'Please check the form.')
    } finally {
        loading.value = false
    }
}

</script>

<template>
    <UCard>
      <template #header>
        <h1 class="text-xl font-semibold">Create account</h1>
        <p class="text-sm text-gray-500">Office Management System</p>
      </template>
  
      <UForm :state="state" class="space-y-4" @submit="onSubmit">
        <UFormField label="Name" name="name" required>
          <UInput v-model="state.name" class="w-full" />
        </UFormField>
  
        <UFormField label="Email" name="email" required>
          <UInput v-model="state.email" type="email" class="w-full" />
        </UFormField>
  
        <UFormField label="Password" name="password" required>
          <UInput v-model="state.password" type="password" class="w-full" />
        </UFormField>
  
        <UFormField label="Confirm password" name="password_confirmation" required>
          <UInput v-model="state.password_confirmation" type="password" class="w-full" />
        </UFormField>
  
        <UButton type="submit" label="Register" block :loading="loading" />
      </UForm>
  
      <template #footer>
        <p class="text-sm text-center text-gray-500">
          Have account?
          <NuxtLink to="/login" class="text-primary font-medium">Sign in</NuxtLink>
        </p>
      </template>
    </UCard>
  </template>