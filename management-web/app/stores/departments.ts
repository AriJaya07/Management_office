import { defineStore } from 'pinia'
import type { Department, PaginatedResponse, PaginationMeta } from '~/types'

export interface DepartmentPayload {
  name: string
  code: string
  manager_id?: number | null
}

export const useDepartmentsStore = defineStore('departments', () => {
  const { $api } = useNuxtApp()

  const items = ref<Department[]>([])
  const meta = ref<PaginationMeta | null>(null)
  const loading = ref(false)

  async function fetch(params: { page?: number, search?: string } = {}): Promise<void> {
    loading.value = true
    try {
      const response = await $api<PaginatedResponse<Department>>('/api/v1/admin/departments', {
        query: {
          page: params.page ?? 1,
          search: params.search || undefined,
        },
      })
      items.value = response.data
      meta.value = response.meta
    } finally {
      loading.value = false
    }
  }

  async function create(payload: DepartmentPayload): Promise<Department> {
    const response = await $api<{ data: Department }>('/api/v1/admin/departments', {
      method: 'POST',
      body: payload,
    })
    return response.data
  }

  async function update(id: number, payload: Partial<DepartmentPayload>): Promise<Department> {
    const response = await $api<{ data: Department }>(`/api/v1/admin/departments/${id}`, {
      method: 'PUT',
      body: payload,
    })
    return response.data
  }

  async function remove(id: number): Promise<void> {
    await $api(`/api/v1/admin/departments/${id}`, { method: 'DELETE' })
  }

  return { items, meta, loading, fetch, create, update, remove }
})
