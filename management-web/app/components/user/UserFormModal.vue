<script setup lang="ts">
import type { User } from '~/types'

const props = defineProps<{
  user?: User | null
}>()

const open = defineModel<boolean>('open', { default: false })

const emit = defineEmits<{
  saved: []
}>()

const usersStore = useUsersStore()
const departmentsStore = useDepartmentsStore()
const toast = useToast()
const { handle } = useApiError()

const isEdit = computed(() => Boolean(props.user))
const loading = ref(false)

const roleOptions = [
  { label: 'Admin', value: 'admin' },
  { label: 'Manager', value: 'manager' },
  { label: 'Employee', value: 'employee' },
]

const state = reactive({
  name: '',
  email: '',
  password: '',
  role: 'employee',
  department_id: undefined as number | undefined,
  position_id: undefined as number | undefined,
  phone: '',
  is_active: true,
})

function close() {
  open.value = false
}

const departmentOptions = computed(() =>
  departmentsStore.items.map(d => ({ label: d.name, value: d.id })),
)

const positionOptions = computed(() => {
  const department = departmentsStore.items.find(d => d.id === state.department_id)
  return (department?.positions ?? []).map(p => ({ label: p.title, value: p.id }))
})

watch(open, (value) => {
  if (!value) {
    return
  }

  if (departmentsStore.items.length === 0) {
    departmentsStore.fetch()
  }

  state.name = props.user?.name ?? ''
  state.email = props.user?.email ?? ''
  state.password = ''
  state.role = props.user?.roles[0] ?? 'employee'
  state.department_id = props.user?.department?.id ?? undefined
  state.position_id = props.user?.position?.id ?? undefined
  state.phone = props.user?.phone ?? ''
  state.is_active = props.user?.is_active ?? true
})

async function onSubmit() {
  loading.value = true
  try {
    const payload = {
      ...state,
      password: state.password || undefined,
      phone: state.phone || null,
      department_id: state.department_id ?? null,
      position_id: state.position_id ?? null,
    }

    if (isEdit.value && props.user) {
      await usersStore.update(props.user.id, payload)
      toast.add({ title: 'User updated', color: 'success' })
    } else {
      await usersStore.create({ ...payload, password: state.password })
      toast.add({ title: 'User created', color: 'success' })
    }

    open.value = false
    emit('saved')
  } catch (error) {
    handle(error)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <UModal v-model:open="open" :title="isEdit ? 'Edit user' : 'Create user'">
    <template #body>
      <UForm :state="state" class="space-y-4" @submit="onSubmit">
        <UFormField label="Name" name="name" required>
          <UInput v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="Email" name="email" required>
          <UInput v-model="state.email" type="email" class="w-full" />
        </UFormField>

        <UFormField
          label="Password"
          name="password"
          :required="!isEdit"
          :help="isEdit ? 'Leave empty to keep the current password.' : undefined"
        >
          <UInput v-model="state.password" type="password" class="w-full" />
        </UFormField>

        <UFormField label="Role" name="role" required>
          <USelect v-model="state.role" :items="roleOptions" class="w-full" />
        </UFormField>

        <div class="grid grid-cols-2 gap-4">
          <UFormField label="Department" name="department_id">
            <USelect
              v-model="state.department_id"
              :items="departmentOptions"
              placeholder="Select department"
              class="w-full"
            />
          </UFormField>

          <UFormField label="Position" name="position_id">
            <USelect
              v-model="state.position_id"
              :items="positionOptions"
              :disabled="!state.department_id"
              placeholder="Select position"
              class="w-full"
            />
          </UFormField>
        </div>

        <UFormField label="Phone" name="phone">
          <UInput v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField label="Active" name="is_active">
          <USwitch v-model="state.is_active" />
        </UFormField>

        <div class="flex justify-end gap-2 pt-2">
          <UButton label="Cancel" variant="ghost" color="neutral" @click="close" />
          <UButton type="submit" :label="isEdit ? 'Save changes' : 'Create user'" :loading="loading" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
