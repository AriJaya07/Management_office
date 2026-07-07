<script setup lang="ts">
import type { TableColumn } from '@nuxt/ui'
import type { User } from '~/types'

definePageMeta({ middleware: 'admin' })

const usersStore = useUsersStore()
const toast = useToast()
const { confirm } = useConfirm()
const { handle } = useApiError()

const search = ref('')
const page = ref(1)
const modalOpen = ref(false)
const selectedUser = ref<User | null>(null)

const columns: TableColumn<User>[] = [
  { accessorKey: 'name', header: 'Name' },
  { accessorKey: 'email', header: 'Email' },
  { accessorKey: 'department', header: 'Department' },
  { accessorKey: 'roles', header: 'Role' },
  { accessorKey: 'is_active', header: 'Status' },
  { id: 'actions', header: '' },
]

async function load() {
  await usersStore.fetch({ page: page.value, search: search.value })
}

watch(page, load)

watchDebounced(search, () => {
  page.value = 1
  load()
}, { debounce: 300 })

onMounted(load)

function openCreate() {
  selectedUser.value = null
  modalOpen.value = true
}

function openEdit(user: User) {
  selectedUser.value = user
  modalOpen.value = true
}

async function onDelete(user: User) {
  const confirmed = await confirm({
    title: 'Delete user',
    message: `Delete ${user.name}? This can be undone by an administrator.`,
    confirmLabel: 'Delete',
  })

  if (!confirmed) {
    return
  }

  try {
    await usersStore.remove(user.id)
    toast.add({ title: 'User deleted', color: 'success' })
    load()
  } catch (error) {
    handle(error)
  }
}
</script>

<template>
  <div>
    <AppPageHeader title="Users" description="Manage accounts, roles, and departments.">
      <template #actions>
        <UButton label="Add user" icon="i-lucide-plus" @click="openCreate" />
      </template>
    </AppPageHeader>

    <div class="mb-4 max-w-xs">
      <UInput
        v-model="search"
        icon="i-lucide-search"
        placeholder="Search name or email..."
        class="w-full"
      />
    </div>

    <CommonDataTable
      :data="usersStore.items"
      :columns="columns"
      :loading="usersStore.loading"
      :meta="usersStore.meta"
      @update:page="(value: number) => page = value"
    >
      <template #department-cell="{ row }">
        {{ row.original.department?.name ?? '—' }}
      </template>

      <template #roles-cell="{ row }">
        <span class="capitalize">{{ row.original.roles.join(', ') }}</span>
      </template>

      <template #is_active-cell="{ row }">
        <CommonStatusBadge :active="row.original.is_active" />
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

    <UserFormModal
      v-model:open="modalOpen"
      :user="selectedUser"
      @saved="load"
    />
  </div>
</template>
