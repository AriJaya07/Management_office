<script setup lang="ts">
import type { Department } from '~/types'

const props = defineProps<{
  department?: Department | null
}>()

const open = defineModel<boolean>('open', { default: false })

const emit = defineEmits<{
  saved: []
}>()

const departmentsStore = useDepartmentsStore()
const toast = useToast()
const { handle } = useApiError()

const isEdit = computed(() => Boolean(props.department))
const loading = ref(false)

const state = reactive({
  name: '',
  code: '',
})

function close() {
  open.value = false
}

watch(open, (value) => {
  if (!value) {
    return
  }

  state.name = props.department?.name ?? ''
  state.code = props.department?.code ?? ''
})

async function onSubmit() {
  loading.value = true
  try {
    if (isEdit.value && props.department) {
      await departmentsStore.update(props.department.id, state)
      toast.add({ title: 'Department updated', color: 'success' })
    } else {
      await departmentsStore.create(state)
      toast.add({ title: 'Department created', color: 'success' })
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
  <UModal v-model:open="open" :title="isEdit ? 'Edit department' : 'Create department'">
    <template #body>
      <UForm :state="state" class="space-y-4" @submit="onSubmit">
        <UFormField label="Name" name="name" required>
          <UInput v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="Code" name="code" required help="Short unique code, e.g. ENG">
          <UInput v-model="state.code" class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2 pt-2">
          <UButton label="Cancel" variant="ghost" color="neutral" @click="close" />
          <UButton type="submit" :label="isEdit ? 'Save changes' : 'Create department'" :loading="loading" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
