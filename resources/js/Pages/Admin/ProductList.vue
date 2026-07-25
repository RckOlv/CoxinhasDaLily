<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  products: Array,
  categories: Array,
})

const editing = ref(null)
const form = ref({ name: '', description: '', price: '', category_id: '', stock_quantity: 0 })
const imagePreview = ref(null)
const imageFile = ref(null)
const saving = ref(false)
const confirmDelete = ref(false)

function formatPrice(value) {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 0,
  }).format(value)
}

function openEdit(product) {
  editing.value = product
  form.value = {
    name: product.name,
    description: product.description || '',
    price: product.price,
    category_id: product.category_id || '',
    stock_quantity: product.stock_quantity,
  }
  imagePreview.value = product.image_path ? '/img/' + product.image_path : null
  imageFile.value = null
  confirmDelete.value = false
}

function closeModal() {
  editing.value = null
  confirmDelete.value = false
}

function onImageChange(e) {
  const file = e.target.files[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

function save() {
  saving.value = true
  const data = new FormData()
  data.append('name', form.value.name)
  data.append('description', form.value.description)
  data.append('price', form.value.price)
  data.append('category_id', form.value.category_id || '')
  data.append('stock_quantity', form.value.stock_quantity)
  data.append('_method', 'PUT')
  if (imageFile.value) {
    data.append('image', imageFile.value)
  }

  router.post(`/admin/productos/${editing.value.id}`, data, {
    onFinish: () => {
      saving.value = false
      closeModal()
    },
    preserveScroll: true,
  })
}

function destroy() {
  router.delete(`/admin/productos/${editing.value.id}`, {
    onFinish: () => {
      closeModal()
    },
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout>
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-cream/95 backdrop-blur-sm border-b border-primary/10">
      <div class="flex items-center justify-between h-14 px-4 sm:px-6 max-w-5xl mx-auto">
        <div class="flex items-center gap-3">
          <Link href="/" class="text-secondary/50 hover:text-secondary transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <polyline points="15 18 9 12 15 6" />
            </svg>
          </Link>
          <h1 class="font-display font-bold text-lg text-secondary">Panel de Lily</h1>
        </div>
        <Link
          href="/"
          class="text-xs text-secondary/40 hover:text-secondary/60 transition-colors"
        >
          Ver tienda
        </Link>
      </div>
    </div>

    <!-- Lista de productos -->
    <div class="px-4 sm:px-6 py-4 max-w-5xl mx-auto pb-28 lg:pb-8">
      <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-secondary/50">
          {{ products.length }} productos
        </p>
      </div>

      <div class="space-y-3">
        <div
          v-for="product in products"
          :key="product.id"
          class="bg-white rounded-2xl border border-primary/10 shadow-sm overflow-hidden"
        >
          <div class="flex gap-3 p-3">
            <!-- Miniatura -->
            <div class="shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-cream">
              <img
                v-if="product.image_path"
                :src="'/img/' + product.image_path"
                :alt="product.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-6 h-6 text-secondary/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="3" y="3" width="18" height="18" rx="2" />
                  <circle cx="8.5" cy="8.5" r="1.5" />
                  <polyline points="21 15 16 10 5 21" />
                </svg>
              </div>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <h3 class="font-display font-semibold text-sm text-secondary truncate">
                    {{ product.name }}
                  </h3>
                  <p class="text-xs text-secondary/50 mt-0.5 line-clamp-2">
                    {{ product.description }}
                  </p>
                </div>
                <span class="shrink-0 text-sm font-bold text-secondary">
                  {{ formatPrice(product.price) }}
                </span>
              </div>

              <div class="flex items-center justify-between mt-2">
                <span
                  v-if="product.category"
                  class="text-[10px] font-medium text-primary-dark bg-primary/10 px-2 py-0.5 rounded-full"
                >
                  {{ product.category.name }}
                </span>
                <span v-else class="text-[10px] text-secondary/30">Sin categoría</span>

                <div class="flex items-center gap-1">
                  <button
                    class="w-7 h-7 rounded-lg bg-secondary/5 flex items-center justify-center
                           text-secondary/50 hover:bg-secondary/10 hover:text-secondary
                           active:scale-90 transition-all"
                  >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                      <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                  </button>
                  <span
                    class="w-9 text-center text-sm font-bold tabular-nums"
                    :class="product.stock_quantity > 0 ? 'text-secondary' : 'text-red-400'"
                  >
                    {{ product.stock_quantity }}
                  </span>
                  <button
                    class="w-7 h-7 rounded-lg bg-secondary/5 flex items-center justify-center
                           text-secondary/50 hover:bg-secondary/10 hover:text-secondary
                           active:scale-90 transition-all"
                  >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                      <line x1="12" y1="5" x2="12" y2="19" />
                      <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Boton Editar -->
          <div class="border-t border-primary/5 px-3 py-2">
            <button
              @click="openEdit(product)"
              class="flex items-center gap-1.5 text-xs font-medium text-secondary/40 hover:text-secondary transition-colors"
            >
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
              </svg>
              Editar
            </button>
          </div>
        </div>
      </div>

      <div v-if="products.length === 0" class="text-center py-16">
        <svg class="w-12 h-12 text-secondary/15 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <p class="text-sm text-secondary/40">No hay productos cargados</p>
      </div>
    </div>

    <!-- Boton flotante Agregar -->
    <div class="fixed bottom-20 lg:bottom-6 right-4 sm:right-6 z-40">
      <button
        class="flex items-center gap-2 py-3.5 px-5 rounded-2xl bg-primary text-secondary font-display font-bold text-sm
               shadow-lg shadow-primary/30 hover:scale-105 active:scale-[0.97] transition-all duration-300"
      >
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Nuevo Producto
      </button>
    </div>

    <!-- Modal Editar -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-200"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
      >
        <div
          v-if="editing"
          class="fixed inset-0 z-[9999] bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center"
          @click.self="closeModal"
        >
          <div
            class="w-full sm:max-w-lg bg-cream rounded-t-3xl sm:rounded-3xl shadow-2xl max-h-[90vh] overflow-y-auto
                   animate-fade-in-up"
          >
            <!-- Header modal -->
            <div class="sticky top-0 bg-cream/95 backdrop-blur-sm border-b border-primary/10 px-5 py-4 flex items-center justify-between rounded-t-3xl z-10">
              <h2 class="font-display font-bold text-base text-secondary">Editar producto</h2>
              <button
                @click="closeModal"
                class="w-8 h-8 rounded-full bg-secondary/5 flex items-center justify-center text-secondary/50
                       hover:bg-secondary/10 hover:text-secondary transition-colors"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="save" class="p-5 space-y-4">
              <!-- Imagen -->
              <div>
                <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Imagen</label>
                <div class="flex items-center gap-3">
                  <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-white border border-primary/10">
                    <img
                      v-if="imagePreview"
                      :src="imagePreview"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-secondary/15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                      </svg>
                    </div>
                  </div>
                  <label
                    class="flex-1 py-2.5 rounded-xl border border-dashed border-primary/20 text-center
                           text-xs font-medium text-secondary/40 hover:border-primary/40 hover:text-secondary/60
                           cursor-pointer transition-colors"
                  >
                    Cambiar foto
                    <input type="file" accept="image/*" class="hidden" @change="onImageChange" />
                  </label>
                </div>
              </div>

              <!-- Nombre -->
              <div>
                <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Nombre</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-4 py-2.5 rounded-xl bg-white border border-primary/10 text-sm text-secondary
                         placeholder:text-secondary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30
                         transition-all"
                  placeholder="Nombre del producto"
                />
              </div>

              <!-- Descripcion -->
              <div>
                <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Descripción</label>
                <textarea
                  v-model="form.description"
                  rows="2"
                  class="w-full px-4 py-2.5 rounded-xl bg-white border border-primary/10 text-sm text-secondary
                         placeholder:text-secondary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30
                         transition-all resize-none"
                  placeholder="Descripción del producto"
                />
              </div>

              <!-- Precio + Stock -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Precio</label>
                  <input
                    v-model="form.price"
                    type="number"
                    required
                    min="0"
                    class="w-full px-4 py-2.5 rounded-xl bg-white border border-primary/10 text-sm text-secondary
                           placeholder:text-secondary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30
                           transition-all tabular-nums"
                    placeholder="0"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Stock</label>
                  <input
                    v-model="form.stock_quantity"
                    type="number"
                    required
                    min="0"
                    class="w-full px-4 py-2.5 rounded-xl bg-white border border-primary/10 text-sm text-secondary
                           placeholder:text-secondary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30
                           transition-all tabular-nums"
                    placeholder="0"
                  />
                </div>
              </div>

              <!-- Categoria -->
              <div>
                <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Categoría</label>
                <select
                  v-model="form.category_id"
                  class="w-full px-4 py-2.5 rounded-xl bg-white border border-primary/10 text-sm text-secondary
                         focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30
                         transition-all appearance-none"
                >
                  <option value="">Sin categoría</option>
                  <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.id"
                  >
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <!-- Botones -->
              <div class="flex gap-3 pt-2">
                <button
                  type="submit"
                  :disabled="saving"
                  class="flex-1 py-3 rounded-xl bg-primary text-secondary font-display font-bold text-sm
                         shadow-md shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all
                         disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ saving ? 'Guardando...' : 'Guardar cambios' }}
                </button>
                <button
                  type="button"
                  @click="closeModal"
                  class="py-3 px-5 rounded-xl border border-secondary/15 text-secondary/50 font-display font-semibold text-sm
                         hover:bg-secondary/5 hover:text-secondary transition-all"
                >
                  Cancelar
                </button>
              </div>

              <!-- Eliminar -->
              <div class="pt-1">
                <Transition
                  enter-active-class="transition-all duration-200"
                  leave-active-class="transition-all duration-200"
                  enter-from-class="opacity-0 -translate-y-1"
                  leave-to-class="opacity-0 -translate-y-1"
                >
                  <div v-if="confirmDelete" class="bg-red-50 rounded-xl p-4 border border-red-100">
                    <p class="text-sm text-red-600 text-center mb-3">
                      ¿Eliminar <strong>{{ editing?.name }}</strong>? Esta acción no se puede deshacer.
                    </p>
                    <div class="flex gap-2">
                      <button
                        type="button"
                        @click="destroy"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-display font-bold text-sm
                               hover:bg-red-600 active:scale-[0.98] transition-all"
                      >
                        Sí, eliminar
                      </button>
                      <button
                        type="button"
                        @click="confirmDelete = false"
                        class="flex-1 py-2.5 rounded-xl border border-red-200 text-red-400 font-display font-semibold text-sm
                               hover:bg-red-50 transition-all"
                      >
                        No, cancelar
                      </button>
                    </div>
                  </div>
                </Transition>
                <button
                  v-if="!confirmDelete"
                  type="button"
                  @click="confirmDelete = true"
                  class="w-full py-2.5 text-center text-xs font-medium text-red-400 hover:text-red-500 transition-colors"
                >
                  Eliminar producto
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
