<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  categories: Array,
})

function logout() {
  router.post(route('logout'))
}

const showModal = ref(false)
const modalMode = ref(null)
const editingCategory = ref(null)
const form = ref({ name: '' })
const saving = ref(false)

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,
  customClass: { popup: 'swal2-font' },
})

function openCreate() {
  form.value = { name: '' }
  modalMode.value = 'create'
  editingCategory.value = null
  showModal.value = true
}

function openEdit(category) {
  form.value = { name: category.name }
  modalMode.value = 'edit'
  editingCategory.value = category
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  modalMode.value = null
  editingCategory.value = null
}

function save() {
  saving.value = true
  const isEdit = modalMode.value === 'edit'

  router.post(isEdit ? `/admin/categorias/${editingCategory.value.id}` : '/admin/categorias', {
    name: form.value.name,
    _method: isEdit ? 'PUT' : 'POST',
  }, {
    onFinish: () => {
      saving.value = false
      closeModal()
      Toast.fire({
        icon: 'success',
        title: isEdit ? 'Categoría actualizada' : 'Categoría creada',
      })
    },
    preserveScroll: true,
  })
}

async function destroy(category) {
  const result = await Swal.fire({
    title: '¿Eliminar categoría?',
    html: `Se eliminará <strong>${category.name}</strong> permanentemente.${category.products_count > 0 ? '<br><small class=\'text-red-500\'>Los productos asociados quedarán sin categoría.</small>' : ''}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#78350F',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    customClass: { popup: 'swal2-font' },
  })

  if (!result.isConfirmed) return

  router.delete(`/admin/categorias/${category.id}`, {
    onFinish: () => {
      Toast.fire({
        icon: 'success',
        title: 'Categoría eliminada',
      })
    },
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout>
    <AdminHeader />

    <!-- Lista -->
    <div class="px-4 sm:px-6 py-4 max-w-5xl mx-auto pb-28 lg:pb-8">
      <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-secondary/50">
          {{ categories.length }} categorías
        </p>
      </div>

      <div v-if="categories.length === 0" class="text-center py-16">
        <svg class="w-12 h-12 text-secondary/15 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
        </svg>
        <p class="font-display font-semibold text-secondary/40">No hay categorías</p>
        <p class="text-sm text-secondary/30 mt-1">Creá la primera para organizar tus productos</p>
      </div>

      <div class="space-y-2">
        <div
          v-for="category in categories"
          :key="category.id"
          class="bg-white rounded-2xl border border-primary/10 shadow-sm p-4 flex items-center justify-between gap-3"
        >
          <div class="min-w-0">
            <h3 class="font-display font-semibold text-sm text-secondary">
              {{ category.name }}
            </h3>
            <p class="text-xs text-secondary/40 mt-0.5">
              {{ category.products_count }} {{ category.products_count === 1 ? 'producto' : 'productos' }}
            </p>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <button
              @click="openEdit(category)"
              class="p-2 rounded-xl text-secondary/30 hover:text-primary hover:bg-primary/10 transition-all"
              title="Editar"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
              </svg>
            </button>
            <button
              @click="destroy(category)"
              class="p-2 rounded-xl text-secondary/30 hover:text-red-500 hover:bg-red-50 transition-all"
              title="Eliminar"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6" />
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Boton flotante -->
    <button
      @click="openCreate"
      class="fixed bottom-24 lg:bottom-8 right-4 sm:right-6 z-40 w-14 h-14 rounded-2xl bg-primary text-secondary shadow-lg shadow-primary/30 flex items-center justify-center active:scale-95 transition-transform"
    >
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
    </button>

    <!-- Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300"
        leave-active-class="transition-all duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
      >
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
          <!-- Overlay -->
          <div class="absolute inset-0 bg-secondary/30 backdrop-blur-sm" @click="closeModal" />

          <!-- Contenido -->
          <div class="relative w-full sm:max-w-md bg-cream rounded-t-3xl sm:rounded-3xl shadow-2xl p-6 animate-slide-up max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
              <h2 class="font-display font-bold text-lg text-secondary">
                {{ modalMode === 'edit' ? 'Editar categoría' : 'Nueva categoría' }}
              </h2>
              <button @click="closeModal" class="text-secondary/30 hover:text-secondary transition-colors">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>

            <form @submit.prevent="save" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-secondary/60 mb-1.5">Nombre</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  @input="form.name = $event.target.value.replace(/[0-9]/g, '')"
                  class="w-full px-4 py-2.5 rounded-xl bg-white border border-primary/10 text-sm text-secondary
                         placeholder:text-secondary/30 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/30
                         transition-all"
                  placeholder="Ej: Congelados"
                />
              </div>

              <button
                type="submit"
                :disabled="saving || !form.name.trim()"
                class="w-full py-3 rounded-xl font-display font-bold text-sm transition-all active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                :class="modalMode === 'edit'
                  ? 'bg-secondary text-cream hover:bg-secondary/90'
                  : 'bg-primary text-secondary hover:bg-primary/90'"
              >
                {{ saving ? 'Guardando...' : (modalMode === 'edit' ? 'Guardar cambios' : 'Crear categoría') }}
              </button>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
