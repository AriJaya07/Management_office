<script setup lang="ts">
const { state, settle } = useConfirm()
</script>

<template>
  <UModal
    v-model:open="state.open"
    :title="state.title"
    :description="state.message"
    @update:open="(value: boolean) => !value && settle(false)"
  >
    <template #footer>
      <div class="flex justify-end gap-2 w-full">
        <UButton
          :label="state.cancelLabel"
          variant="ghost"
          color="neutral"
          @click="settle(false)"
        />
        <UButton
          :label="state.confirmLabel"
          color="error"
          @click="settle(true)"
        />
      </div>
    </template>
  </UModal>
</template>
