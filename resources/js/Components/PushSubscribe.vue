<script setup>
import { ref, onMounted } from 'vue'

const supported = ref(false)
const subscribed = ref(false)
const showPrompt = ref(false)
const dismissed = ref(false)

onMounted(async () => {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return

  supported.value = true

  const reg = await navigator.serviceWorker.register('/sw.js')

  const existing = await reg.pushManager.getSubscription()
  if (existing) {
    subscribed.value = true
    return
  }

  const wasDismissed = localStorage.getItem('push_dismissed')
  if (!wasDismissed) {
    setTimeout(() => { showPrompt.value = true }, 5000)
  }
})

async function subscribe() {
  try {
    const reg = await navigator.serviceWorker.ready
    const vapidKey = import.meta.env.VITE_VAPID_PUBLIC_KEY

    if (!vapidKey) {
      console.warn('VAPID public key not configured')
      return
    }

    const permission = await Notification.requestPermission()
    if (permission !== 'granted') return

    const subscription = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(vapidKey),
    })

    const json = subscription.toJSON()
    await fetch('/api/push/subscribe', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({
        endpoint: json.endpoint,
        keys: json.keys,
      }),
    })

    subscribed.value = true
    showPrompt.value = false
  } catch (e) {
    console.error('Push subscribe error:', e)
  }
}

function dismiss() {
  showPrompt.value = false
  dismissed.value = true
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
              <div class="flex gap-2 mt-3">
                <button
                  @click="subscribe"
                  class="px-4 py-1.5 rounded-lg bg-primary text-secondary text-xs font-bold hover:bg-primary-dark hover:text-white transition-colors"
                >
                  Activar
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
