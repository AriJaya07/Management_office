<script setup lang="ts" generic="T">
import type { TableColumn } from '@nuxt/ui'
import type { PaginationMeta } from '~/types'

defineProps<{
  data: T[]
  columns: TableColumn<T>[]
  loading?: boolean
  meta?: PaginationMeta | null
}>()

const emit = defineEmits<{
  'update:page': [page: number]
}>()
</script>

<template>
  <UCard :ui="{ body: 'p-0 sm:p-0' }">
    <UTable
      :data="data"
      :columns="columns"
      :loading="loading"
    >
      <template v-for="(_, name) in $slots" #[name]="slotProps" :key="name">
        <slot :name="name" v-bind="slotProps" />
      </template>
    </UTable>

    <div
      v-if="!loading && data.length === 0"
      class="flex flex-col items-center gap-2 py-12 text-gray-500"
    >
      <UIcon name="i-lucide-inbox" class="size-8" />
      <p>No records found.</p>
    </div>

    <div
      v-if="meta && meta.total > meta.per_page"
      class="flex justify-between items-center px-4 py-3 border-t border-gray-200 dark:border-gray-800"
    >
      <p class="text-sm text-gray-500">
        {{ meta.total }} total
      </p>
      <UPagination
        :page="meta.current_page"
        :total="meta.total"
        :items-per-page="meta.per_page"
        @update:page="(page: number) => emit('update:page', page)"
      />
    </div>
  </UCard>
</template>
