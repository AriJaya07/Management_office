<script setup lang="ts">
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const links = [
  { label: 'Dashboard', icon: 'i-lucide-layout-dashboard', to: '/' },
]
</script>

<template>
  <div class="min-h-screen flex bg-gray-50 dark:bg-gray-950">
    <aside class="w-64 shrink-0 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col">
      <div class="h-16 flex items-center px-6 font-bold text-lg">
        Office Manager
      </div>
      <nav class="flex-1 px-3 space-y-1">
        <UButton
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          :icon="link.icon"
          :label="link.label"
          variant="ghost"
          color="neutral"
          block
          class="justify-start"
        />
      </nav>
      <div class="p-3 border-t border-gray-200 dark:border-gray-800">
        <div class="px-3 py-2 text-sm">
          <p class="font-medium">{{ auth.user?.name }}</p>
          <p class="text-gray-500 text-xs">{{ auth.user?.roles.join(', ') }}</p>
        </div>
        <UButton
          label="Logout"
          icon="i-lucide-log-out"
          variant="ghost"
          color="error"
          block
          class="justify-start"
          @click="auth.logout()"
        />
      </div>
    </aside>
    <main class="flex-1 p-8 overflow-auto">
      <slot />
    </main>
  </div>
</template>
