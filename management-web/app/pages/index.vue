<script setup lang="ts">
definePageMeta({ middleware: 'auth' })

const auth = useAuthStore()
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-2">Welcome, {{ auth.user?.name }}</h1>
    <p class="text-gray-500">You are logged in as {{ auth.user?.roles.join(', ') }}.</p>
  </div>
</template>
