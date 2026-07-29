<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useCart } from '@/Composables/useCart'
import axios from 'axios'

const { whatsapp_number } = usePage().props

const props = defineProps({
  open: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'complete'])

const { items, total, clearCart } = useCart()

const form = ref({
  name: '',
  phone: '',
  address: '',
  delivery_method: 'pickup',
  payment_method: 'efectivo',
})

const paymentMethods = [
  { value: 'efectivo', label: 'Efectivo' },
  { value: 'transferencia', label: 'Transferencia' },
  { value: 'mercadopago', label: 'MercadoPago' },
]

const showErrors = ref(false)
const sending = ref(false)

const isValid = computed(() => {
  if (!form.value.name.trim()) return false
  if (!form.value.phone.trim()) return false
  if (form.value.delivery_method === 'envio' && !form.value.address.trim()) return false
  return true
})

function formatPrice(value) {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 2,
  }).format(value)
}

function buildWhatsAppMessage() {
  const lines = [
    '¡Hola Lily! 🙋‍♀️ Quiero hacer un pedido en *Coxinhas da Lily* 🇦🇷🇧🇷',
    '',
    '📦 *Mi pedido:*',
  ]

  items.value.forEach((item) => {
    lines.push(`  • ${item.quantity}x ${item.name} — ${formatPrice(item.price * item.quantity)}`)
  })

  lines.push('')
  lines.push(`💰 *Total: ${formatPrice(total.value)}*`)
  lines.push('')

  if (form.value.delivery_method === 'pickup') {
    lines.push('🏠 Retiro en tu domicilio')
  } else {
    lines.push(`🚚 Envío a: ${form.value.address}`)
    lines.push('   (Coordinar traslado - no incluido en el precio)')
  }

  const payLabel = paymentMethods.find((p) => p.value === form.value.payment_method)?.label
  lines.push(`💳 Pago: ${payLabel}`)
  lines.push(`📱 WhatsApp: ${form.value.phone}`)
  lines.push(`👤 A nombre de: ${form.value.name}`)

  return lines.join('\n')
}

async function confirm() {
  if (!isValid.value) {
    showErrors.value = true
    return
  }

  sending.value = true

  // Capturar suscripción push si está disponible
  let pushEndpoint = null
  try {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
      const reg = await navigator.serviceWorker.ready
      const sub = await reg.pushManager.getSubscription()
      if (sub) {
        pushEndpoint = sub.endpoint
      }
    }
  } catch {}

  // Crear pedido en BD
  try {
    await axios.post('/api/checkout', {
      client_name: form.value.name,
      client_whatsapp: form.value.phone,
      delivery_method: form.value.delivery_method,
      delivery_address: form.value.address || null,
      payment_method: form.value.payment_method,
      push_endpoint: pushEndpoint,
      items: items.value.map((item) => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price,
      })),
    })
  } catch {
    // Si falla, igual abrimos WhatsApp
  }

  const text = buildWhatsAppMessage()
  const phone = whatsapp_number
  const url = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
  window.open(url, '_blank')

  sending.value = false
  emit('complete')
}
</script>

<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition name="fade">
      <div
        v-if="open"
        class="fixed inset-0 z-[80] bg-secondary/30 backdrop-blur-sm"
        @click="emit('close')"
      />
    </Transition>

    <!-- Modal -->
    <Transition name="modal">
      <div
        v-if="open"
        class="fixed inset-0 z-[90] flex items-end sm:items-center justify-center p-4"
      >
        <div
          class="bg-cream rounded-3xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-primary/10"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-primary/5">
            <div>
              <h2 class="text-lg font-display font-bold text-secondary">Confirmar Pedido</h2>
              <p class="text-xs text-stone-400 mt-0.5">Te redirigimos a WhatsApp</p>
            </div>
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

          <!-- Formulario -->
          <div class="px-5 pb-5 space-y-4">
            <!-- Nombre -->
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Tu nombre</label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ej: María García"
                @input="form.name = $event.target.value.replace(/[0-9]/g, '')"
                class="w-full px-4 py-3 rounded-xl bg-white border-2 text-sm text-secondary placeholder-stone-300
                       transition-colors outline-none
                       focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]
                       invalid:border-red-400"
                :class="showErrors && !form.name.trim() ? 'border-red-300' : 'border-primary/15'"
              />
              <p v-if="showErrors && !form.name.trim()" class="text-xs text-red-400 mt-1">Ingresá tu nombre</p>
            </div>

            <!-- Teléfono -->
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Tu WhatsApp</label>
              <input
                v-model="form.phone"
                type="tel"
                inputmode="numeric"
                pattern="[0-9+\-\s]*"
                placeholder="Ej: +54 9 11 1234-5678"
                @input="form.phone = $event.target.value.replace(/[^0-9+\-\s]/g, '')"
                class="w-full px-4 py-3 rounded-xl bg-white border-2 text-sm text-secondary placeholder-stone-300
                       transition-colors outline-none
                       focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]
                       invalid:border-red-400"
                :class="showErrors && !form.phone.trim() ? 'border-red-300' : 'border-primary/15'"
              />
              <p v-if="showErrors && !form.phone.trim()" class="text-xs text-red-400 mt-1">Ingresá tu número de WhatsApp</p>
            </div>

            <!-- Método de entrega -->
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-2 uppercase tracking-wide">Entrega</label>
              <div class="flex gap-2">
                <label
                  class="flex-1 flex items-center gap-2.5 bg-white rounded-xl px-4 py-3 border-2 cursor-pointer transition-all"
                  :class="
                    form.delivery_method === 'pickup'
                      ? 'border-primary shadow-sm shadow-primary/10'
                      : 'border-primary/10 hover:border-primary/25'
                  "
                >
                  <input
                    v-model="form.delivery_method"
                    type="radio"
                    value="pickup"
                    class="sr-only"
                  />
                  <span
                    class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                    :class="form.delivery_method === 'pickup' ? 'border-primary bg-primary' : 'border-stone-300'"
                  >
                    <span v-if="form.delivery_method === 'pickup'" class="w-1.5 h-1.5 rounded-full bg-white" />
                  </span>
                  <div>
                    <p class="text-sm font-semibold text-secondary">Retiro en Domicilio</p>
                    <p class="text-[10px] text-stone-400">Sin costo</p>
                  </div>
                </label>
                <label
                  class="flex-1 flex items-center gap-2.5 bg-white rounded-xl px-4 py-3 border-2 cursor-pointer transition-all"
                  :class="
                    form.delivery_method === 'envio'
                      ? 'border-primary shadow-sm shadow-primary/10'
                      : 'border-primary/10 hover:border-primary/25'
                  "
                >
                  <input
                    v-model="form.delivery_method"
                    type="radio"
                    value="envio"
                    class="sr-only"
                  />
                  <span
                    class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors"
                    :class="form.delivery_method === 'envio' ? 'border-primary bg-primary' : 'border-stone-300'"
                  >
                    <span v-if="form.delivery_method === 'envio'" class="w-1.5 h-1.5 rounded-full bg-white" />
                  </span>
                  <div>
                    <p class="text-sm font-semibold text-secondary">Envío</p>
                    <p class="text-[10px] text-stone-400">Coordiná el traslado (Uber, etc.)</p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Dirección (si es envío) -->
            <div v-if="form.delivery_method === 'envio'">
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Dirección de entrega</label>
              <input
                v-model="form.address"
                type="text"
                placeholder="Calle, número, piso..."
                class="w-full px-4 py-3 rounded-xl bg-white border-2 text-sm text-secondary placeholder-stone-300
                       transition-colors outline-none
                       focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
                :class="showErrors && form.delivery_method === 'envio' && !form.address.trim() ? 'border-red-300' : 'border-primary/15'"
              />
              <p v-if="showErrors && form.delivery_method === 'envio' && !form.address.trim()" class="text-xs text-red-400 mt-1">
                Ingresá tu dirección
              </p>
              <p class="text-[11px] text-stone-400 mt-1.5">⚠️ Coordiná el traslado con un servicio de transporte (Uber, motocaixo, etc.). El envío no está incluido en el precio.</p>
            </div>

            <!-- Método de pago -->
            <div>
              <label class="block text-xs font-semibold text-secondary/60 mb-1.5 uppercase tracking-wide">Forma de pago</label>
              <select
                v-model="form.payment_method"
                class="w-full px-4 py-3 rounded-xl bg-white border-2 border-primary/15 text-sm text-secondary
                       transition-colors outline-none appearance-none cursor-pointer
                       focus:border-primary focus:shadow-[0_0_0_3px_rgba(234,179,8,0.15)]"
              >
                <option
                  v-for="pm in paymentMethods"
                  :key="pm.value"
                  :value="pm.value"
                >
                  {{ pm.label }}
                </option>
              </select>
            </div>

            <!-- Resumen -->
            <div class="bg-white rounded-xl p-4 space-y-1.5 border border-primary/8">
              <div v-for="item in items" :key="item.product_id" class="flex justify-between text-xs">
                <span class="text-stone-400">{{ item.quantity }}x {{ item.name }}</span>
                <span class="font-medium text-secondary">{{ formatPrice(item.price * item.quantity) }}</span>
              </div>
              <div class="border-t border-primary/10 pt-1.5 flex justify-between">
                <span class="text-sm font-bold text-secondary">Total</span>
                <span class="text-sm font-bold text-primary-dark">{{ formatPrice(total) }}</span>
              </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3">
              <button
                @click="emit('close')"
                class="flex-1 py-3 rounded-xl border-2 border-primary/15 text-sm font-semibold text-stone-400
                       hover:bg-primary/5 active:scale-[0.98] transition-all"
              >
                Cancelar
              </button>
              <button
                @click="confirm"
                :disabled="sending"
                class="flex-1 py-3 rounded-xl bg-[#25D366] text-white text-sm font-bold
                       shadow-lg shadow-[#25D366]/25 active:scale-[0.98] transition-all
                       flex items-center justify-center gap-2 disabled:opacity-60"
              >
                <svg v-if="!sending" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                <svg v-else class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                  <path d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" fill="currentColor" />
                </svg>
                {{ sending ? 'Procesando...' : 'Enviar por WhatsApp' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
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

.modal-enter-active {
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.modal-leave-active {
  transition: all 0.2s ease-in;
}
.modal-enter-from {
  opacity: 0;
  transform: translateY(40px) scale(0.95);
}
.modal-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.98);
}
</style>
