<script setup>
import { ref, computed } from 'vue'
import { useCart } from '@/Composables/useCart'

const props = defineProps({
  product: { type: Object, required: true },
  badge: { type: String, default: null },
})

const { addItem } = useCart()

const quantity = ref(1)
const added = ref(false)

const hasStock = computed(() => props.product.stock_quantity > 0)

function increment() {
  if (quantity.value < props.product.stock_quantity) quantity.value++
}

function decrement() {
  if (quantity.value > 1) quantity.value--
}

function add() {
  for (let i = 0; i < quantity.value; i++) {
    addItem(props.product)
  }
  added.value = true
  quantity.value = 1
  setTimeout(() => { added.value = false }, 1200)
}

function formatPrice(value) {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 0,
  }).format(value)
}
</script>

<template>
  <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-primary/8 flex flex-col transition-shadow active:shadow-md">
    <!-- Imagen -->
    <div
      class="relative aspect-square bg-cream-dark overflow-hidden"
      :class="{ 'opacity-50 grayscale': !hasStock }"
    >
      <img
        v-if="product.image_path"
        :src="product.image_path"
        :alt="product.name"
        loading="lazy"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center">
          <svg class="w-8 h-8 text-primary-dark/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <path d="M21 15l-5-5L5 21" />
          </svg>
        </div>
      </div>

      <!-- Badge de categoría -->
      <span
        v-if="badge"
        class="absolute top-2 left-2 px-2 py-0.5 bg-secondary text-white text-[10px] font-bold rounded-md shadow-sm backdrop-blur-sm uppercase tracking-wide"
      >
        {{ badge }}
      </span>

      <!-- Badge AGOTADO -->
      <span
        v-if="!hasStock"
        class="absolute top-2 right-2 px-2.5 py-1 bg-red-700 text-white text-[10px] font-bold rounded-md shadow-sm uppercase tracking-wide"
      >
        Agotado
      </span>

      <!-- Precio badge -->
      <span
        class="absolute bottom-2 left-2 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-sm font-bold shadow-sm border border-primary/10"
        :class="hasStock ? 'text-primary-dark' : 'text-gray-400'"
      >
        {{ formatPrice(product.price) }}
      </span>
    </div>

    <!-- Info -->
    <div class="p-3 flex flex-col flex-1">
      <h3
        class="font-display font-semibold text-sm leading-tight mb-0.5 line-clamp-1"
        :class="hasStock ? 'text-secondary' : 'text-gray-400'"
      >
        {{ product.name }}
      </h3>
      <p
        v-if="product.description"
        class="text-xs line-clamp-2 mb-3 flex-1"
        :class="hasStock ? 'text-stone-400' : 'text-gray-300'"
      >
        {{ product.description }}
      </p>
      <p
        v-if="product.units_per_package"
        class="text-[10px] font-semibold text-primary-dark mb-2"
      >
        {{ product.units_per_package }} unidades por bolsa
      </p>
      <div v-else class="flex-1" />

      <!-- Selector de cantidad + Agregar -->
      <div v-if="hasStock" class="space-y-2">
        <div class="flex items-center justify-center gap-1">
          <button
            @click="decrement"
            :disabled="quantity <= 1"
            class="w-9 h-9 rounded-lg border border-primary/20 flex items-center justify-center text-secondary
                   transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed
                   hover:bg-primary/10"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
          </button>
          <span class="w-10 text-center text-sm font-bold text-secondary tabular-nums">
            {{ quantity }}
          </span>
          <button
            @click="increment"
            :disabled="quantity >= product.stock_quantity"
            class="w-9 h-9 rounded-lg border border-primary/20 flex items-center justify-center text-secondary
                   transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed
                   hover:bg-primary/10"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
          </button>
        </div>

        <button
          @click="add"
          class="w-full py-2.5 rounded-xl text-sm font-bold
                 shadow-md shadow-primary/25 active:scale-[0.97] active:shadow-sm
                 transition-all duration-150 flex items-center justify-center gap-1.5"
          :class="added
            ? 'bg-green-500 text-white'
            : 'bg-primary text-secondary hover:bg-primary-dark hover:text-white'"
        >
          <template v-if="!added">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Agregar {{ quantity > 1 ? `(${quantity})` : '' }}
          </template>
          <template v-else>
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12" />
            </svg>
            Agregado
          </template>
        </button>
      </div>

      <button
        v-else
        disabled
        class="w-full py-2.5 rounded-xl bg-gray-300 text-gray-500 text-sm font-bold
               cursor-not-allowed transition-all duration-150 flex items-center justify-center gap-1.5"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <circle cx="12" cy="12" r="10" />
          <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
        </svg>
        Sin Stock
      </button>
    </div>
  </div>
</template>
