interface ConfirmOptions {
  title?: string
  message: string
  confirmLabel?: string
  cancelLabel?: string
}

interface ConfirmState extends Required<ConfirmOptions> {
  open: boolean
  resolve: ((value: boolean) => void) | null
}

const state = reactive<ConfirmState>({
  open: false,
  title: 'Are you sure?',
  message: '',
  confirmLabel: 'Confirm',
  cancelLabel: 'Cancel',
  resolve: null,
})

export function useConfirm() {
  function confirm(options: ConfirmOptions): Promise<boolean> {
    state.title = options.title ?? 'Are you sure?'
    state.message = options.message
    state.confirmLabel = options.confirmLabel ?? 'Confirm'
    state.cancelLabel = options.cancelLabel ?? 'Cancel'
    state.open = true

    return new Promise<boolean>((resolve) => {
      state.resolve = resolve
    })
  }

  function settle(value: boolean): void {
    state.resolve?.(value)
    state.resolve = null
    state.open = false
  }

  return { state, confirm, settle }
}
