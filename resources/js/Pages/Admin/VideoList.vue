<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({ videos: Array })

const form = useForm({ videos: [], titles: [] })
const uploading = ref(false)
const previewUrls = ref([])
const previewTitles = ref([])

function onFileChange(e) {
  const files = Array.from(e.target.files)
  form.videos = files
  form.titles = files.map(f => f.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' '))
  previewUrls.value = files.map(f => URL.createObjectURL(f))
  previewTitles.value = [...form.titles]
}

function updateTitle(index, value) {
  form.titles[index] = value
  previewTitles.value[index] = value
}

function upload() {
  if (!form.videos.length) return
  uploading.value = true
  form.post(route('admin.videos.store'), {
    onFinish: () => {
      uploading.value = false
      form.videos = []
      form.titles = []
      previewUrls.value = []
      previewTitles.value = []
    },
  })
}

function toggleActive(video) {
  router.put(route('admin.videos.update', video.id), {
    is_active: !video.is_active,
  }, { preserveScroll: true })
}

function removeVideo(video) {
  Swal.fire({
    title: 'Eliminar video',
    text: '¿Seguro que querés eliminar este video?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DC2626',
    cancelButtonColor: '#78350F',
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.videos.destroy', video.id), { preserveScroll: true })
    }
  })
}

function formatSize(bytes) {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB'
  return (bytes / 1048576).toFixed(1) + ' MB'
}
</script>

<template>
  <AppLayout>
    <Head title="Videos - Admin" />
    <AdminHeader />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
      <!-- Upload -->
      <div class="bg-white rounded-2xl border border-primary/10 p-5 sm:p-6 mb-6">
        <h2 class="font-display font-bold text-lg text-secondary mb-4">Subir videos</h2>

        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-primary/25 rounded-2xl cursor-pointer hover:border-primary/50 transition-colors bg-cream/50">
          <svg class="w-8 h-8 text-primary/40 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="5 3 19 12 5 21 5 3" />
          </svg>
          <span class="text-sm text-secondary/50">Elegí uno o más videos (MP4, max 50MB)</span>
          <input type="file" multiple accept="video/mp4,video/webm,video/quicktime" @change="onFileChange" class="hidden" />
        </label>

        <!-- Previews -->
        <div v-if="previewUrls.length" class="mt-4 space-y-3">
          <div v-for="(url, i) in previewUrls" :key="i" class="flex items-center gap-3 bg-cream rounded-xl p-3">
            <div class="shrink-0 w-20 h-14 rounded-lg overflow-hidden bg-secondary/10">
              <video :src="url" class="w-full h-full object-cover" muted />
            </div>
            <div class="flex-1 min-w-0">
              <input
                :value="previewTitles[i]"
                @input="updateTitle(i, $event.target.value)"
                type="text"
                class="w-full px-3 py-1.5 rounded-lg bg-white border border-primary/10 text-sm text-secondary
                       focus:outline-none focus:ring-2 focus:ring-primary/30"
                placeholder="Título del video"
              />
              <p class="text-[10px] text-secondary/30 mt-1">{{ formatSize(form.videos[i]?.size || 0) }}</p>
            </div>
          </div>
        </div>

        <button
          v-if="form.videos.length"
          @click="upload"
          :disabled="uploading"
          class="mt-4 px-6 py-2.5 rounded-xl bg-primary text-secondary font-display font-bold text-sm
                 shadow-md shadow-primary/20 hover:scale-105 active:scale-[0.97] transition-all
                 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ uploading ? 'Subiendo...' : `Subir ${form.videos.length} video(s)` }}
        </button>
      </div>

      <!-- Video list -->
      <div class="bg-white rounded-2xl border border-primary/10 p-5 sm:p-6">
        <h2 class="font-display font-bold text-lg text-secondary mb-4">
          Videos actuales
          <span class="text-sm font-normal text-secondary/40">({{ videos.length }})</span>
        </h2>

        <div v-if="!videos.length" class="text-center py-12 text-secondary/30">
          <svg class="w-12 h-12 mx-auto mb-3 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="5 3 19 12 5 21 5 3" />
          </svg>
          <p class="text-sm">No hay videos cargados</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="video in videos"
            :key="video.id"
            class="flex items-center gap-3 bg-cream rounded-xl p-3"
            :class="{ 'opacity-40': !video.is_active }"
          >
            <div class="shrink-0 w-24 h-16 rounded-lg overflow-hidden bg-secondary/10 relative">
              <video :src="'/storage/' + video.video_path" class="w-full h-full object-cover" muted preload="metadata" />
              <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                  <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-secondary truncate">{{ video.title || 'Sin título' }}</p>
              <p class="text-[10px] text-secondary/30">{{ video.video_path }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="toggleActive(video)"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"
                :class="video.is_active ? 'bg-white text-secondary border border-primary/15' : 'bg-primary text-secondary'"
              >
                {{ video.is_active ? 'Visible' : 'Oculto' }}
              </button>
              <button
                @click="removeVideo(video)"
                class="p-2 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-500 transition-colors"
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
  </AppLayout>
</template>
