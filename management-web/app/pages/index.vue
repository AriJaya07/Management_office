<script setup lang="ts">
import type { ApiResponse, DashboardStats } from '~/types'

definePageMeta({ middleware: 'auth' })

const auth = useAuthStore()
const { $api } = useNuxtApp()

const stats = ref<DashboardStats | null>(null)

onMounted(async () => {
  if (!auth.hasRole('admin')) {
    return
  }

  try {
    const response = await $api<ApiResponse<DashboardStats>>('/api/v1/admin/stats')
    stats.value = response.data
  } catch {
    stats.value = null
  }
})
</script>

<template>
  <div>
    <AppPageHeader
      :title="`Welcome, ${auth.user?.name}`"
      :description="`You are logged in as ${auth.user?.roles.join(', ')}.`"
    />

    <div v-if="stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <CommonStatCard label="Total users" :value="stats.total_users" icon="i-lucide-users" />
      <CommonStatCard label="Active users" :value="stats.active_users" icon="i-lucide-user-check" />
      <CommonStatCard label="Departments" :value="stats.total_departments" icon="i-lucide-building-2" />
      <CommonStatCard label="Positions" :value="stats.total_positions" icon="i-lucide-badge-check" />
    </div>

    <UCard v-else-if="!auth.hasRole('admin')">
      <div class="flex items-center gap-3 text-gray-500">
        <UIcon name="i-lucide-info" class="size-5" />
        <p>Your workspace is ready. More features are coming soon.</p>
      </div>
    </UCard>
  </div>
</template>
