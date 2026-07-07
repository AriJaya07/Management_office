export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig()
  
    const api = $fetch.create({
      baseURL: config.public.apiBase,
      credentials: 'include',
      headers: { Accept: 'application/json' },
  
      async onRequest({ options }) {
        const method = (options.method ?? 'GET').toUpperCase()
  
        if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
          let token = useCookie('XSRF-TOKEN').value
  
          if (!token) {
            await $fetch('/sanctum/csrf-cookie', {
              baseURL: config.public.apiBase,
              credentials: 'include',
            })
            token = useCookie('XSRF-TOKEN').value
          }
  
          if (token) {
            options.headers.set('X-XSRF-TOKEN', token)
          }
        }
      },
    })
  
    return { provide: { api } }
  })
  