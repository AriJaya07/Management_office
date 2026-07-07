<script setup lang="ts">
defineProps<{
  label: string
  value: number | string
  icon: string
}>()
</script>

<template>
  <UCard>
    <div class="flex items-center gap-4">
      <div class="rounded-full bg-primary/10 p-3">
        <UIcon :name="icon" class="size-6 text-primary" />
      </div>
      <div>
        <p class="text-sm text-gray-500">{{ label }}</p>
        <p class="text-2xl font-bold">{{ value }}</p>
      </div>
    </div>
  </UCard>
</template>
