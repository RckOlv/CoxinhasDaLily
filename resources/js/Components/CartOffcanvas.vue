<script setup>
import { useCart } from '@/Composables/useCart'
import CheckoutModal from './CheckoutModal.vue'
import { ref } from 'vue'

const props = defineProps({
  open: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

const { items, total, updateQuantity, removeItem, clearCart } = useCart()
const showCheckout = ref(false)

function formatPrice(value) {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 0,
  }).format(value)
}

function onCheckoutComplete() {
  showCheckout.value = false
  clearCart()
  emit('close')
}
</script>

<template>
  <!-- Backdrop -->
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="open"
        class="fixed inset-0 z-[60] bg-secondary/30 backdrop-blur-sm"
        @click="emit('close')"
      />
    </Transition>

    <!-- Panel -->
    <Transition name="slide-up">
      <div
        v-if="open"
        class="fixed inset-x-0 bottom-0 z-[70] bg-cream rounded-t-3xl shadow-2xl max-h-[85vh] flex flex-col border-t border-primary/10"
      >
        <!-- Handle -->
        <div class="flex justify-center pt-3 pb-1">
          <div class="w-10 h-1 rounded-full bg-primary/30" />
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-primary/5">
          <h2 class="text-lg font-display font-bold text-secondary">Tu Pedido</h2>
          <button
            @click="emit('close')"
            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-primary/10 transition-colors"
          >
            <svg class="w-5 h-5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <!-- Lista de items -->
        <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3">
          <!-- Vacío -->
          <div v-if="items.length === 0" class="py-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-primary/10 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-primary-dark/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
              </svg>
            </div>
            <p class="text-secondary/60 text-sm font-medium">Tu carrito está vacío</p>
            <p class="text-stone-300 text-xs mt-1">Agregá algo del menú para empezar</p>
          </div>

          <!-- Item -->
          <div
            v-for="item in items"
            :key="item.product_id"
            class="flex items-center gap-3 bg-white rounded-xl p-3 border border-primary/5"
          >
            <!-- Thumbnail -->
            <div class="w-14 h-14 rounded-lg bg-cream-dark overflow-hidden shrink-0 border border-primary/5">
              <img
                v-if="item.image_path"
                :src="item.image_path"
                :alt="item.name"
                loading="lazy"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-6 h-6 text-primary/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="3" y="3" width="18" height="18" rx="2" />
                  <circle cx="8.5" cy="8.5" r="1.5" />
                  <path d="M21 15l-5-5L5 21" />
                </svg>
              </div>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-secondary truncate">{{ item.name }}</h4>
              <p class="text-xs text-stone-400">{{ formatPrice(item.price) }} c/u</p>
            </div>

            <!-- Controles cantidad -->
            <div class="flex items-center gap-1.5">
              <button
                @click="updateQuantity(item.product_id, item.quantity - 1)"
                class="w-7 h-7 rounded-lg bg-cream-dark border border-primary/10 flex items-center justify-center text-stone-400 hover:bg-primary/10 active:scale-95 transition-all"
              >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                  <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
              </button>
              <span class="w-6 text-center text-sm font-bold text-secondary">{{ item.quantity }}</span>
              <button
                @click="updateQuantity(item.product_id, item.quantity + 1)"
                class="w-7 h-7 rounded-lg bg-primary text-secondary flex items-center justify-center hover:bg-primary-dark hover:text-white active:scale-95 transition-all shadow-sm shadow-primary/20"
              >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                  <line x1="12" y1="5" x2="12" y2="19" />
                  <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="items.length > 0" class="border-t border-primary/10 px-5 py-4 space-y-3 bg-white/50">
          <!-- Total -->
          <div class="flex items-center justify-between">
            <span class="text-sm text-stone-400 font-medium">Total</span>
            <span class="text-xl font-display font-bold text-secondary">{{ formatPrice(total) }}</span>
          </div>

          <!-- Botón completar pedido -->
          <button
            @click="showCheckout = true"
            class="w-full py-3.5 rounded-2xl bg-secondary text-white font-bold text-base
                   shadow-lg shadow-secondary/25 active:scale-[0.98] transition-all
                   hover:bg-secondary-dark flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            Completar Pedido por WhatsApp
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Checkout Modal -->
  <CheckoutModal
    :open="showCheckout"
    @close="showCheckout = false"
    @complete="onCheckoutComplete"
  />
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active {
  transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.slide-up-leave-active {
  transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}
</style>
