<script setup lang="ts">
const auth = useAuthStore()

const links = computed(() => {
  const items = [
    { label: 'Dashboard', icon: 'i-lucide-layout-dashboard', to: '/' },
  ]

  if (auth.can('users.view')) {
    items.push({ label: 'Users', icon: 'i-lucide-users', to: '/users' })
  }

  if (auth.can('departments.view')) {
    items.push({ label: 'Departments', icon: 'i-lucide-building-2', to: '/departments' })
  }

  return items
})
</script>

<template>
  <aside class="w-64 shrink-0 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col">
    <div class="h-16 flex items-center gap-2 px-6 font-bold text-lg">
      <UIcon name="i-lucide-briefcase" class="text-primary size-6" />
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
        <p class="text-gray-500 text-xs capitalize">{{ auth.user?.roles.join(', ') }}</p>
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
</template>
