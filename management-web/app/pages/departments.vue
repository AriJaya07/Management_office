<script setup lang="ts">
import type { TableColumn } from '@nuxt/ui'
import type { Department } from '~/types'

definePageMeta({ middleware: 'admin' })

const departmentsStore = useDepartmentsStore()
const toast = useToast()
const { confirm } = useConfirm()
const { handle } = useApiError()

const search = ref('')
const page = ref(1)
const modalOpen = ref(false)
const selectedDepartment = ref<Department | null>(null)

const columns: TableColumn<Department>[] = [
  { accessorKey: 'name', header: 'Name' },
  { accessorKey: 'code', header: 'Code' },
  { accessorKey: 'manager', header: 'Manager' },
  { accessorKey: 'users_count', header: 'Members' },
  { id: 'actions', header: '' },
]

async function load() {
  await departmentsStore.fetch({ page: page.value, search: search.value })
}

watch(page, load)

watchDebounced(search, () => {
  page.value = 1
  load()
}, { debounce: 300 })

onMounted(load)

function openCreate() {
  selectedDepartment.value = null
  modalOpen.value = true
}

function openEdit(department: Department) {
  selectedDepartment.value = department
  modalOpen.value = true
}

async function onDelete(department: Department) {
  const confirmed = await confirm({
    title: 'Delete department',
    message: `Delete ${department.name}? Members will be detached.`,
    confirmLabel: 'Delete',
  })

  if (!confirmed) {
    return
  }

  try {
    await departmentsStore.remove(department.id)
    toast.add({ title: 'Department deleted', color: 'success' })
    load()
  } catch (error) {
    handle(error)
  }
}
</script>

<template>
  <div>
    <AppPageHeader title="Departments" description="Organize teams and positions.">
      <template #actions>
        <UButton label="Add department" icon="i-lucide-plus" @click="openCreate" />
      </template>
    </AppPageHeader>

    <div class="mb-4 max-w-xs">
      <UInput
        v-model="search"
        icon="i-lucide-search"
        placeholder="Search departments..."
        class="w-full"
      />
    </div>

    <CommonDataTable
      :data="departmentsStore.items"
      :columns="columns"
      :loading="departmentsStore.loading"
      :meta="departmentsStore.meta"
      @update:page="(value: number) => page = value"
    >
      <template #manager-cell="{ row }">
        {{ row.original.manager?.name ?? '—' }}
      </template>

      <template #users_count-cell="{ row }">
        {{ row.original.users_count ?? 0 }}
      </template>

      <template #actions-cell="{ row }">
        <div class="flex justify-end gap-1">
          <UButton
            icon="i-lucide-pencil"
            variant="ghost"
            color="neutral"
            size="sm"
            @click="openEdit(row.original)"
          />
          <UButton
            icon="i-lucide-trash-2"
            variant="ghost"
            color="error"
            size="sm"
            @click="onDelete(row.original)"
          />
        </div>
      </template>
    </CommonDataTable>

    <DepartmentFormModal
      v-model:open="modalOpen"
      :department="selectedDepartment"
      @saved="load"
    />
  </div>
</template>
