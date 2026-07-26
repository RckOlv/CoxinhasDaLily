<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CategoryFilter from '@/Components/CategoryFilter.vue'
import ProductCard from '@/Components/ProductCard.vue'

const props = defineProps({
  categories: { type: Array, required: true },
})

const selectedCategoryId = ref(null)

const allProducts = computed(() =>
  props.categories.flatMap((c) => c.products)
)

const filteredProducts = computed(() => {
  if (selectedCategoryId.value !== null) {
    const cat = props.categories.find((c) => c.id === selectedCategoryId.value)
    return cat ? cat.products : []
  }
  return allProducts.value
})

function onCategorySelect(catId) {
  selectedCategoryId.value = catId
}
</script>

<template>
  <AppLayout>
    <!-- Filtro de categorías sticky -->
    <div class="sticky top-16 z-40 bg-cream/95 backdrop-blur-sm border-b border-primary/5">
      <div class="max-w-7xl mx-auto">
        <CategoryFilter
          :categories="categories"
          :selected="selectedCategoryId"
          @select="onCategorySelect"
        />
      </div>
    </div>

    <!-- Grilla de productos -->
    <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 max-w-7xl mx-auto w-full">
      <div v-if="selectedCategoryId !== null" class="flex items-center gap-2 mb-3 sm:mb-4">
        <button
          @click="selectedCategoryId = null"
          class="w-7 h-7 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary hover:bg-secondary/20 transition-colors"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
        <span class="text-sm font-display font-semibold text-secondary">
          {{ filteredProducts.length }} producto{{ filteredProducts.length !== 1 ? 's' : '' }}
        </span>
      </div>

      <div
        v-if="filteredProducts.length > 0"
        class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6"
      >
        <ProductCard
          v-for="product in filteredProducts"
          :key="product.id"
          :product="product"
          :badge="product.badge"
        />
      </div>

      <!-- Empty state -->
      <div v-else class="py-16 text-center">
        <div class="w-20 h-20 mx-auto rounded-full bg-primary/10 flex items-center justify-center mb-4">
          <svg class="w-10 h-10 text-primary-dark/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10" />
            <path d="M8 15s1.5-2 4-2 4 2 4 2" />
            <line x1="9" y1="9" x2="9.01" y2="9" stroke-width="2" />
            <line x1="15" y1="9" x2="15.01" y2="9" stroke-width="2" />
          </svg>
        </div>
        <p class="text-secondary/60 text-sm font-medium">No hay productos acá</p>
        <p class="text-secondary/40 text-xs mt-1">Probá con otra categoría</p>
      </div>
    </div>
  </AppLayout>
</template>
