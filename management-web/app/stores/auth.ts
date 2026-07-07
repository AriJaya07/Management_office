import { defineStore } from 'pinia'
import type { User } from '~/types'

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const { $api } = useNuxtApp()

    const isAuthenticated = computed(() => user.value !== null)
    const hasRole = (role: string) => user.value?.roles.includes(role) ?? false

    async function fetchUser(): Promise<void> {
        try {
            const response = await $api<{ data: User }>('/api/user')
            user.value = response.data
        } catch {
            user.value = null
        }
    }

    async function login(credentials: { email: string, password: string }): Promise<void> {
        const response = await $api<{user: User }>('/api/login', {
            method: 'POST',
            body: credentials,
        })
        user.value = response.user
    }

    async function register(payload: {
        name: string
        email: string
        password: string
        password_confirmation: string
    }): Promise<void> {
        const response = await $api<{ user: User }>('/api/register', {
            method: 'POST',
            body: payload,
        })
        user.value = response.user
    }

    async function logout(): Promise<void> {
        await $api('/api/logout', { method: 'POST' })
        user.value = null
        navigateTo('/login')
    }

    return { user, isAuthenticated, hasRole, fetchUser, login, register, logout }
})