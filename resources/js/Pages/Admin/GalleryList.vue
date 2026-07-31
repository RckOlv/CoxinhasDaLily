<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'
import LoadingOverlay from '@/Components/LoadingOverlay.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({ images: Array })

const form = useForm({ images: [] })
const uploading = ref(false)
const previewUrls = ref([])

function onFileChange(e) {
  const files = Array.from(e.target.files)
  form.images = files
  previewUrls.value = files.map(f => URL.createObjectURL(f))
}

function upload() {
  if (!form.images.length) return
  uploading.value = true
  form.post(route('admin.gallery.store'), {
    onFinish: () => {
      uploading.value = false
      form.images = []
      previewUrls.value = []
    },
  })
}

function toggleActive(image) {
  router.put(route('admin.gallery.update', image.id), {
    is_active: !image.is_active,
  }, { preserveScroll: true })
}

function removeImage(image) {
  Swal.fire({
    title: 'Eliminar foto',
    text: '¿Seguro que querés eliminar esta foto?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DC2626',
    cancelButtonColor: '#78350F',
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.gallery.destroy', image.id), { preserveScroll: true })
    }
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Galería - Admin" />
    <AdminHeader />
    <LoadingOverlay :loading="uploading" />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
      <!-- Upload -->
      <div class="bg-white rounded-2xl border border-primary/10 p-5 sm:p-6 mb-6">
        <h2 class="font-display font-bold text-lg text-secondary mb-4">Subir fotos</h2>

        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-primary/25 rounded-2xl cursor-pointer hover:border-primary/50 transition-colors bg-cream/50">
          <svg class="w-8 h-8 text-primary/40 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <polyline points="21 15 16 10 5 21" />
          </svg>
          <span class="text-sm text-secondary/50">Elegí una o más fotos</span>
          <input type="file" multiple accept="image/*" @change="onFileChange" class="hidden" />
        </label>

        <!-- Previews -->
        <div v-if="previewUrls.length" class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-2">
          <div v-for="(url, i) in previewUrls" :key="i" class="relative rounded-xl overflow-hidden aspect-square">
            <img :src="url" class="w-full h-full object-cover" />
          </div>
        </div>

        <button
          v-if="form.images.length"
          @click="upload"
          :disabled="uploading"
          class="mt-4 px-6 py-2.5 rounded-xl bg-primary text-secondary font-display font-bold text-sm
                 shadow-md shadow-primary/20 hover:scale-105 active:scale-[0.97] transition-all
                 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ uploading ? 'Subiendo...' : `Subir ${form.images.length} foto(s)` }}
        </button>
      </div>

      <!-- Image grid -->
      <div class="bg-white rounded-2xl border border-primary/10 p-5 sm:p-6">
        <h2 class="font-display font-bold text-lg text-secondary mb-4">
          Fotos actuales
          <span class="text-sm font-normal text-secondary/40">({{ images.length }})</span>
        </h2>

        <div v-if="!images.length" class="text-center py-12 text-secondary/30">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <polyline points="21 15 16 10 5 21" />
          </svg>
          <p class="text-sm">No hay fotos en la galería</p>
        </div>

        <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
          <div
            v-for="img in images"
            :key="img.id"
            class="group relative rounded-xl overflow-hidden aspect-square border border-primary/10"
            :class="{ 'opacity-40': !img.is_active }"
          >
            <img :src="'/storage/' + img.image_path" :alt="img.alt" class="w-full h-full object-cover" loading="lazy" />

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end justify-center pb-3 opacity-0 group-hover:opacity-100">
              <div class="flex gap-2">
                <button
                  @click="toggleActive(img)"
                  class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"
                  :class="img.is_active ? 'bg-white/90 text-secondary' : 'bg-primary text-secondary'"
                >
                  {{ img.is_active ? 'Ocultar' : 'Mostrar' }}
                </button>
                <button
                  @click="removeImage(img)"
                  class="px-3 py-1.5 rounded-lg bg-red-500/90 text-white text-xs font-bold hover:bg-red-600 transition-colors"
                >
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
