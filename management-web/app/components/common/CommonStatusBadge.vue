<script setup lang="ts">
const props = defineProps<{
  active: boolean
}>()

const color = computed(() => (props.active ? 'success' : 'error'))
const label = computed(() => (props.active ? 'Active' : 'Inactive'))
</script>

<template>
  <UBadge :color="color" variant="subtle" :label="label" />
</template>
