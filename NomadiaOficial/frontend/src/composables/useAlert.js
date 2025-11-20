import { Modal } from 'bootstrap'

let modalInstance = null
let modalComponent = null

export function useAlert() {
  const setModalComponent = (component) => {
    modalComponent = component
  }

  const showAlert = (options) => {
    if (!modalComponent) {
      console.error('Modal component not initialized')
      return
    }

    // Determinar tipo basado en el mensaje
    let type = options.type || 'info'
    const msg = options.message || options

    // Auto-detectar tipo basado en emojis o palabras clave
    if (typeof msg === 'string') {
      if (msg.includes('✅') || msg.toLowerCase().includes('exitosamente') || msg.toLowerCase().includes('éxito')) {
        type = 'success'
      } else if (msg.includes('❌') || msg.toLowerCase().includes('error')) {
        type = 'error'
      } else if (msg.includes('⚠️') || msg.toLowerCase().includes('advertencia')) {
        type = 'warning'
      }
    }

    // Configurar el modal
    modalComponent.show({
      type,
      title: options.title,
      message: typeof options === 'string' ? options : msg,
      btnText: options.btnText
    })

    // Mostrar el modal
    const modalElement = document.getElementById('globalAlertModal')
    if (modalElement) {
      if (!modalInstance) {
        modalInstance = new Modal(modalElement)
      }
      modalInstance.show()
    }
  }

  return {
    setModalComponent,
    showAlert
  }
}
