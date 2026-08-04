<script setup>
import { computed, onMounted, ref } from 'vue'

const CONSENT_KEY = 'cookie_consent'

const hasChoice = ref(false)

const show = computed(() => !hasChoice.value)

function applyAnalytics() {
  if (!window.__GA_ID) return
  if (typeof window.gtag !== 'function') return
  window.gtag('consent', 'update', {
    ad_storage: 'granted',
    analytics_storage: 'granted',
  })
  window.gtag('config', window.__GA_ID, { anonymize_ip: true })
}

function accept() {
  localStorage.setItem(CONSENT_KEY, 'accepted')
  hasChoice.value = true
  applyAnalytics()
}

function reject() {
  localStorage.setItem(CONSENT_KEY, 'rejected')
  hasChoice.value = true
}

onMounted(() => {
  const stored = localStorage.getItem(CONSENT_KEY)
  if (stored === 'accepted') applyAnalytics()
  hasChoice.value = !!stored
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="cookie-enter-active"
      leave-active-class="cookie-leave-active"
      enter-from-class="cookie-enter-from"
      leave-to-class="cookie-leave-to"
    >
      <div
        v-if="show"
        class="fixed bottom-0 inset-x-0 z-[9000] p-4 sm:p-5"
      >
        <div class="max-w-3xl mx-auto bg-secondary text-white rounded-2xl shadow-2xl border border-primary/20 p-5 sm:p-6">
          <p class="text-sm leading-relaxed text-white/80">
            Usamos cookies para que el sitio funcione y, con tu permiso, cookies de analítica
            (Google Analytics) para mejorar la experiencia. Podés
            <a href="/cookies" class="text-primary underline">leer nuestra Política de Cookies</a>.
          </p>
          <div class="mt-4 flex flex-wrap gap-3">
            <button
              type="button"
              class="flex-1 min-w-[140px] bg-primary text-white text-sm font-bold py-2.5 px-4 rounded-xl hover:opacity-90 transition-opacity"
              @click="accept"
            >
              Aceptar todas
            </button>
            <button
              type="button"
              class="flex-1 min-w-[140px] bg-transparent border border-white/30 text-white/70 text-sm font-semibold py-2.5 px-4 rounded-xl hover:border-white/60 hover:text-white transition-colors"
              @click="reject"
            >
              Solo esenciales
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.cookie-enter-active,
.cookie-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.cookie-enter-from,
.cookie-leave-to {
  transform: translateY(100%);
  opacity: 0;
}
</style>
