import { defineStore } from 'pinia'
import type { PaginatedResponse, PaginationMeta, User } from '~/types'

export interface UserPayload {
  name: string
  email: string
  password?: string
  role: string
  department_id?: number | null
  position_id?: number | null
  phone?: string | null
  join_date?: string | null
  is_active?: boolean
}

export const useUsersStore = defineStore('users', () => {
  const { $api } = useNuxtApp()

  const items = ref<User[]>([])
  const meta = ref<PaginationMeta | null>(null)
  const loading = ref(false)

  async function fetch(params: { page?: number, search?: string, role?: string } = {}): Promise<void> {
    loading.value = true
    try {
      const response = await $api<PaginatedResponse<User>>('/api/v1/admin/users', {
        query: {
          page: params.page ?? 1,
          search: params.search || undefined,
          role: params.role || undefined,
        },
      })
      items.value = response.data
      meta.value = response.meta
    } finally {
      loading.value = false
    }
  }

  async function create(payload: UserPayload): Promise<User> {
    const response = await $api<{ data: User }>('/api/v1/admin/users', {
      method: 'POST',
      body: payload,
    })
    return response.data
  }

  async function update(id: number, payload: Partial<UserPayload>): Promise<User> {
    const response = await $api<{ data: User }>(`/api/v1/admin/users/${id}`, {
      method: 'PUT',
      body: payload,
    })
    return response.data
  }

  async function remove(id: number): Promise<void> {
    await $api(`/api/v1/admin/users/${id}`, { method: 'DELETE' })
  }

  return { items, meta, loading, fetch, create, update, remove }
})
