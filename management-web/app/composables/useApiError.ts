import type { FetchError } from 'ofetch'

export function useApiError() {
  const toast = useToast()

  function handle(error: unknown, fallback = 'Something went wrong.'): void {
    const fetchError = error as FetchError
    const validationErrors = fetchError.data?.errors as Record<string, string[]> | undefined

    const description = validationErrors
      ? Object.values(validationErrors).flat().join(' ')
      : fetchError.data?.message ?? fallback

    toast.add({ title: 'Error', description, color: 'error' })
  }

  return { handle }
}
