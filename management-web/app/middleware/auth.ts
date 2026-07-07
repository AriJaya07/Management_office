import { useAuthStore } from "~/stores/auth"

export default defineNuxtRouteMiddleware(async () => {
    const auth = useAuthStore()

    if (!auth.user) {
        await auth.fetchUser()
    }

    if (!auth.isAuthenticated) {
        return navigateTo('/login')
    }
})