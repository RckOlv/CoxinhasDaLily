<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const supported = ref(false)
const subscribed = ref(false)
const showPrompt = ref(false)
const loading = ref(false)
const error = ref('')
const success = ref('')
const permission = ref('default')

onMounted(async () => {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
    console.log('[Push] Browser not supported')
    return
  }

  supported.value = true
  permission.value = Notification.permission

  if (Notification.permission === 'denied') {
    console.log('[Push] Notifications blocked by user')
    return
  }

  try {
    const reg = await navigator.serviceWorker.register('/sw.js')
    console.log('[Push] Service worker registered')

    const existing = await reg.pushManager.getSubscription()
    if (existing) {
      subscribed.value = true
      console.log('[Push] Already subscribed')
      return
    }

    const wasDismissed = localStorage.getItem('push_dismissed')
    if (!wasDismissed) {
      setTimeout(() => { showPrompt.value = true }, 5000)
    }
  } catch (e) {
    console.error('[Push] Registration failed:', e)
  }
})

async function subscribe() {
  loading.value = true
  error.value = ''

  try {
    const reg = await navigator.serviceWorker.ready
    const vapidKey = import.meta.env.VITE_VAPID_PUBLIC_KEY

    if (!vapidKey) {
      error.value = 'Configuración pendiente. Contactá al administrador.'
      loading.value = false
      return
    }

    const perm = await Notification.requestPermission()
    permission.value = perm

    if (perm !== 'granted') {
      error.value = 'Permiso de notificaciones denegado. Activalo en la configuración del navegador.'
      loading.value = false
      return
    }

    const subscription = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(vapidKey),
    })

    const json = subscription.toJSON()
    try {
      await axios.post('/api/push/subscribe', {
        endpoint: json.endpoint,
        keys: json.keys,
      })
      subscribed.value = true
      showPrompt.value = false
      success.value = '¡Notificaciones activadas!'
      setTimeout(() => { success.value = '' }, 3000)
    } catch {
      error.value = 'Error al guardar suscripción.'
    }
  } catch (e) {
    console.error('[Push] Subscribe error:', e)
    error.value = 'Error al activar notificaciones.'
  } finally {
    loading.value = false
  }
}

function dismiss() {
  showPrompt.value = false
  localStorage.setItem('push_dismissed', '1')
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const rawData = window.atob(base64)
  const outputArray = new Uint8Array(rawData.length)
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i)
  }
  return outputArray
}
</script>

<template>
  <Teleport to="body">
    <!-- Toast de éxito -->
    <Transition
      enter-active-class="transition-all duration-300"
      leave-active-class="transition-all duration-300"
      enter-from-class="opacity-0 -translate-y-4"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div
        v-if="success"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-lg"
      >
        {{ success }}
      </div>
    </Transition>

    <!-- Prompt de suscripción -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      leave-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showPrompt && supported && !subscribed"
        class="fixed bottom-24 left-4 right-4 z-50 sm:left-auto sm:right-6 sm:max-w-sm"
      >
        <div class="bg-white rounded-2xl shadow-2xl border border-primary/15 p-5 relative">
          <button @click="dismiss" class="absolute top-3 right-3 text-stone-300 hover:text-stone-500 transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
          <div class="flex items-start gap-3">
            <div class="shrink-0 w-10 h-10 rounded-xl bg-primary/15 flex items-center justify-center">
              <svg class="w-5 h-5 text-primary-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" />
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-display font-bold text-sm text-secondary">¿Querés enterarte?</p>
              <p class="text-xs text-secondary/50 mt-0.5">Recibí notificaciones cuando Lily confirme tu pedido o haya ofertas.</p>

              <!-- Error -->
              <p v-if="error" class="text-xs text-red-500 mt-2 bg-red-50 rounded-lg px-2 py-1.5">{{ error }}</p>

              <div class="flex gap-2 mt-3">
                <button
                  @click="subscribe"
                  :disabled="loading"
                  class="px-4 py-1.5 rounded-lg bg-primary text-secondary text-xs font-bold
                         hover:bg-primary-dark hover:text-white transition-all
                         disabled:opacity-50 disabled:cursor-not-allowed
                         flex items-center gap-1.5"
                >
                  <svg v-if="loading" class="w-3 h-3 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10" stroke-dasharray="30 60" />
                  </svg>
                  {{ loading ? 'Activando...' : 'Activar' }}
                </button>
                <button
                  @click="dismiss"
                  class="px-3 py-1.5 rounded-lg text-xs font-medium text-secondary/40 hover:text-secondary/60 transition-colors"
                >
                  Ahora no
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
