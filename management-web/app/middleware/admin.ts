export default defineNuxtRouteMiddleware(async () => {
  const auth = useAuthStore()

  if (!auth.user) {
    await auth.fetchUser()
  }

  if (!auth.isAuthenticated) {
    return navigateTo('/login')
  }

  if (!auth.hasRole('admin')) {
    return navigateTo('/')
  }
})
