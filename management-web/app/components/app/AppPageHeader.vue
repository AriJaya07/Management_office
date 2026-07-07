<script setup lang="ts">
defineProps<{
  title: string
  description?: string
}>()
</script>

<template>
  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold">{{ title }}</h1>
      <p v-if="description" class="text-gray-500 mt-1">{{ description }}</p>
    </div>
    <div class="shrink-0">
      <slot name="actions" />
    </div>
  </div>
</template>
