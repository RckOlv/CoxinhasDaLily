<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Reveal from '@/Components/Reveal.vue'

const { whatsapp_number, gallery_images, videos: dbVideos } = usePage().props

const galleryImages = gallery_images.map(img => ({
  src: '/storage/' + img.image_path,
  alt: img.alt || 'Foto de galería',
}))

const carouselImages = galleryImages.map((img) => img.src)
const currentSlide = ref(0)
let slideTimer = null

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % carouselImages.length
}

onMounted(() => {
  slideTimer = setInterval(nextSlide, 5000)
})

onBeforeUnmount(() => {
  clearInterval(slideTimer)
})

const videos = dbVideos.map(v => ({
  src: '/storage/' + v.video_path,
  title: v.title || 'Video',
}))

const courseFeatures = [
  'De una misma masa, aprenderás a hacer más de 3 tipos de salados con diferentes sabores',
  'Empanaditas, banderines de salchicha, arrolladitos, bombas de queso y pastelón brasilero',
  'Además de freír, te enseño a vender congelados para tus clientes',
  'Te llega un ebook completo',
]

const playingVideos = ref({})
const videoRefs = ref({})

function togglePlay(src) {
  const el = videoRefs.value[src]
  if (!el) return
  if (el.paused) {
    el.play().catch(() => {})
    playingVideos.value[src] = true
  } else {
    el.pause()
    playingVideos.value[src] = false
  }
}

const imageIndex = ref(null)
const videoIndex = ref(null)
const touchStartX = ref(0)
const touchEndX = ref(0)

function openImage(i) {
  imageIndex.value = i
  document.body.style.overflow = 'hidden'
}

function openVideo(i) {
  videoIndex.value = i
  document.body.style.overflow = 'hidden'
}

function closeMedia() {
  imageIndex.value = null
  videoIndex.value = null
  document.body.style.overflow = ''
}

function prevImage() {
  if (imageIndex.value === null) return
  imageIndex.value = (imageIndex.value - 1 + galleryImages.length) % galleryImages.length
}

function nextImage() {
  if (imageIndex.value === null) return
  imageIndex.value = (imageIndex.value + 1) % galleryImages.length
}

function prevVideo() {
  if (videoIndex.value === null) return
  videoIndex.value = (videoIndex.value - 1 + videos.length) % videos.length
}

function nextVideo() {
  if (videoIndex.value === null) return
  videoIndex.value = (videoIndex.value + 1) % videos.length
}

function onTouchStart(e) {
  touchStartX.value = e.changedTouches[0].screenX
}

function onTouchEnd(e) {
  touchEndX.value = e.changedTouches[0].screenX
  const diff = touchStartX.value - touchEndX.value
  if (Math.abs(diff) < 50) return
  if (imageIndex.value !== null) {
    diff > 0 ? nextImage() : prevImage()
  } else if (videoIndex.value !== null) {
    diff > 0 ? nextVideo() : prevVideo()
  }
}

function handleKeydown(e) {
  if (e.key === 'Escape') closeMedia()
  if (imageIndex.value !== null) {
    if (e.key === 'ArrowLeft') prevImage()
    if (e.key === 'ArrowRight') nextImage()
  }
  if (videoIndex.value !== null) {
    if (e.key === 'ArrowLeft') prevVideo()
    if (e.key === 'ArrowRight') nextVideo()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <AppLayout>
    <!-- Hero Section -->
    <section class="relative overflow-hidden min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen flex items-center">
      <!-- Carrusel de fondo -->
      <div class="hero-carousel absolute inset-0">
        <div
          v-for="(src, i) in carouselImages"
          :key="i"
          class="hero-slide"
          :class="{ active: currentSlide === i }"
          :style="{ backgroundImage: `url(${src})` }"
        />
      </div>

      <!-- Overlay oscuro -->
      <div class="absolute inset-0 bg-black/40" />

      <!-- Contenido -->
      <div class="relative z-10 px-5 py-24 sm:py-32 lg:py-40 w-full max-w-7xl mx-auto text-center">
        <!-- Logo en Hero -->
        <div class="mb-8 animate-fade-in">
          <div class="inline-block bg-white/90 rounded-3xl px-8 py-6 sm:px-10 sm:py-8 shadow-2xl">
            <img src="/img/logolily.png" alt="Coxinhas da Lily" class="h-28 sm:h-36 lg:h-44 mx-auto" />
          </div>
        </div>

        <p
          class="text-white/80 text-sm sm:text-base lg:text-lg max-w-lg mx-auto mb-10 animate-fade-in-up delay-100"
        >
          El auténtico sabor de Brasil, hecho con amor en Posadas
        </p>

        <!-- CTA Hero -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center max-w-lg mx-auto animate-fade-in-up delay-300">
          <Link
            href="/productos"
            class="py-4 px-8 rounded-2xl bg-primary text-secondary font-display font-bold text-base
                   shadow-lg shadow-black/30 hover:scale-105 active:scale-[0.97] transition-all duration-300
                   flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            Hacer Pedido
          </Link>
          <Link
            href="/eventos"
            class="py-4 px-8 rounded-2xl border-2 border-white/30 text-white font-display font-bold text-base
                   hover:bg-white/10 hover:scale-105 active:scale-[0.97] transition-all duration-300
                   flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            Solicitar Evento
          </Link>
        </div>
      </div>
    </section>

    <!-- ¿Quiénes somos? -->
    <Reveal>
    <section class="bg-cream py-12 sm:py-16">
      <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
          <!-- Lado A: Foto -->
          <div class="flex justify-center animate-fade-in-up">
            <img
              src="/img/fotolily.jpg"
              alt="Lily - Fundadora de Coxinhas da Lily"
              loading="lazy"
              class="w-full max-w-sm rounded-2xl shadow-lg object-cover transition-all duration-300 hover:shadow-xl"
            />
          </div>

          <!-- Lado B: Texto -->
          <div class="text-center md:text-left animate-fade-in-up delay-200">
            <h2 class="font-display font-bold text-xl sm:text-2xl text-secondary mb-3">
              ¿Quién soy?
            </h2>
            <p class="text-secondary/70 text-sm sm:text-base leading-relaxed">
              Hace años traigo los sabores de mi tierra. Preparo tandas frescas todos los días
              para que tengas lo mejor en tu freezer o en tus eventos.
            </p>
          </div>
        </div>
      </div>
    </section>
    </Reveal>

    <!-- Videos de producción -->
    <Reveal>
    <section class="bg-secondary py-12 sm:py-16">
      <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
        <h2 class="font-display font-bold text-xl sm:text-2xl text-white text-center mb-2">
          Detrás de escena
        </h2>
        <p class="text-white/60 text-sm text-center mb-1">
          Mirá cómo preparamos todo con amor
        </p>
        <p v-if="videos.length > 2" class="text-xs text-white/25 text-center mb-5 lg:hidden">
          ← Deslizá para ver más →
        </p>
        <div class="flex gap-3 overflow-x-auto scroll-sm snap-x snap-mandatory pb-4 -mx-5 px-5 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:snap-none">
          <div
            v-for="(video, vi) in videos"
            :key="video.src"
            class="group rounded-2xl overflow-hidden bg-secondary-dark relative
                   transition-all duration-300 hover:shadow-xl
                   shrink-0 w-[70%] sm:w-auto snap-center cursor-pointer"
            @click="openVideo(vi)"
          >
            <video
              :ref="el => { videoRefs[video.src] = el }"
              :src="video.src"
              muted
              loop
              playsinline
              preload="auto"
              class="w-full aspect-[3/4] object-cover"
            />
            <!-- Overlay con boton play -->
            <div
              class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 z-10 pointer-events-none"
              :class="playingVideos[video.src]
                ? 'bg-black/10 opacity-0 group-hover:opacity-100'
                : 'bg-black/20 opacity-100'"
            >
              <button
                @click.stop="togglePlay(video.src)"
                class="pointer-events-auto w-14 h-14 rounded-full bg-primary/90 flex items-center justify-center shadow-lg backdrop-blur-sm
                       hover:scale-110 active:scale-95 transition-transform duration-200"
              >
                <svg v-if="!playingVideos[video.src]" class="w-6 h-6 text-secondary ml-1" viewBox="0 0 24 24" fill="currentColor">
                  <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
                <svg v-else class="w-6 h-6 text-secondary" viewBox="0 0 24 24" fill="currentColor">
                  <rect x="6" y="4" width="4" height="16" rx="1" />
                  <rect x="14" y="4" width="4" height="16" rx="1" />
                </svg>
              </button>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent pointer-events-none" />
            <div class="absolute bottom-0 left-0 right-0 p-3 pointer-events-none">
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="currentColor">
                  <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
                <span class="text-white text-xs font-display font-semibold">{{ video.title }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    </Reveal>

    <!-- Galería -->
    <Reveal>
    <section class="bg-cream-dark py-12 sm:py-16">
      <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
        <h2 class="font-display font-bold text-xl sm:text-2xl text-secondary text-center mb-2">
          Nuestros productos
        </h2>
        <p v-if="galleryImages.length > 2" class="text-xs text-secondary/30 text-center mb-5 lg:hidden">
          ← Deslizá para ver más →
        </p>
        <div class="gallery-scroll">
          <div
            v-for="(img, i) in galleryImages"
            :key="img.src"
            class="rounded-2xl overflow-hidden border border-primary/10 shadow-sm cursor-pointer
                   transition-all duration-300 hover:shadow-md hover:border-primary/25"

            @click="openImage(i)"
          >
            <img
              :src="img.src"
              :alt="img.alt"
              class="w-full aspect-[4/3] object-cover"
              loading="lazy"
            />
          </div>
        </div>
      </div>
    </section>
    </Reveal>

    <!-- Image Lightbox -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-200"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
      >
        <div
          v-if="imageIndex !== null"
          class="fixed inset-0 z-[9999] bg-black/85 backdrop-blur-sm flex items-center justify-center select-none"
          @touchstart.passive="onTouchStart"
          @touchend.passive="onTouchEnd"
        >
          <div class="relative w-full h-full flex items-center justify-center p-4 sm:p-8">
            <button
              @click="closeMedia"
              class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm
                     flex items-center justify-center text-white hover:bg-white/20 transition-colors z-20"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>

            <span class="absolute top-4 left-4 sm:top-6 sm:left-6 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-white text-xs font-semibold z-20">
              {{ imageIndex + 1 }} / {{ galleryImages.length }}
            </span>

            <button
              @click="prevImage"
              class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 backdrop-blur-sm
                     items-center justify-center text-white hover:bg-white/20 transition-colors z-20"
            >
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
              </svg>
            </button>

            <img
              :key="imageIndex"
              :src="galleryImages[imageIndex].src"
              :alt="galleryImages[imageIndex].alt"
              class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain animate-scale-in pointer-events-none"
            />

            <p v-if="galleryImages.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-white/60 text-[11px] font-medium z-20 sm:hidden">
              ← Deslizá para ver más →
            </p>

            <button
              @click="nextImage"
              class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 backdrop-blur-sm
                     items-center justify-center text-white hover:bg-white/20 transition-colors z-20"
            >
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6" />
              </svg>
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Video Lightbox -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-200"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
      >
        <div
          v-if="videoIndex !== null"
          class="fixed inset-0 z-[9999] bg-black/85 backdrop-blur-sm flex items-center justify-center select-none"
          @touchstart.passive="onTouchStart"
          @touchend.passive="onTouchEnd"
        >
          <div class="relative w-full h-full flex items-center justify-center p-4 sm:p-8">
            <button
              @click="closeMedia"
              class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm
                     flex items-center justify-center text-white hover:bg-white/20 transition-colors z-20"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>

            <span class="absolute top-4 left-4 sm:top-6 sm:left-6 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-white text-xs font-semibold z-20">
              {{ videoIndex + 1 }} / {{ videos.length }}
            </span>

            <button
              @click="prevVideo"
              class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 backdrop-blur-sm
                     items-center justify-center text-white hover:bg-white/20 transition-colors z-20"
            >
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
              </svg>
            </button>

            <video
              :key="videoIndex"
              :src="videos[videoIndex].src"
              controls
              autoplay
              muted
              playsinline
              class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl animate-scale-in"
            />

            <p v-if="videos.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-white/60 text-[11px] font-medium z-20 sm:hidden">
              ← Deslizá para ver más →
            </p>

            <button
              @click="nextVideo"
              class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 backdrop-blur-sm
                     items-center justify-center text-white hover:bg-white/20 transition-colors z-20"
            >
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6" />
              </svg>
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Sección Eventos -->
    <Reveal>
    <section class="bg-cream py-14 sm:py-20">
      <div class="max-w-lg mx-auto px-5 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/15 flex items-center justify-center mb-5">
          <svg class="w-8 h-8 text-primary-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
        </div>
        <h2 class="font-display font-bold text-xl sm:text-2xl text-secondary mb-2">
          ¿Te gustaría contratar nuestros servicios?
        </h2>
        <p class="text-secondary/50 text-sm mb-3 max-w-md mx-auto leading-relaxed">
          Llevamos el auténtico sabor de Brasil a cumpleaños, casamientos, eventos empresariales y celebraciones.
        </p>
        <p class="text-secondary/40 text-xs mb-6">
          Máximo 100 personas - Retiro en domicilio
        </p>
        <Link
          href="/eventos"
          class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-secondary text-cream font-display font-bold text-sm
                 shadow-lg shadow-secondary/20 hover:scale-105 active:scale-[0.97] transition-all duration-300"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          Armar mi evento
        </Link>
      </div>
    </section>
    </Reveal>

    <!-- Sección Cursos -->
    <Reveal>
    <section id="cursos" class="bg-cream py-14 sm:py-20">
      <div class="max-w-lg mx-auto px-5 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/15 flex items-center justify-center mb-5">
          <svg class="w-8 h-8 text-primary-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
          </svg>
        </div>
        <h2 class="font-display font-bold text-xl sm:text-2xl text-secondary mb-2">
          Te enseño todo lo que sé sobre coxinhas
        </h2>
        <p class="text-secondary/50 text-sm mb-5 max-w-md mx-auto leading-relaxed">
          7 clases en Instagram privado con el paso a paso para hacer las famosas coxinhas y risolis brasileros.
        </p>
        <div class="text-left bg-white rounded-2xl border border-primary/10 p-5 mb-5 space-y-2.5">
          <p class="text-xs font-semibold text-secondary/60 uppercase tracking-wide mb-1">Qué vas a aprender</p>
          <div v-for="(item, i) in courseFeatures" :key="i" class="flex items-start gap-2">
            <svg class="w-4 h-4 text-green-600 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12" />
            </svg>
            <p class="text-sm text-secondary/70 leading-snug">{{ item }}</p>
          </div>
          <p class="text-xs text-secondary/40 mt-3">
            Instagram privado + ebook completo · Clases a tu ritmo
          </p>
        </div>
        <a
          :href="`https://wa.me/${whatsapp_number}?text=Hola%20Lily%2C%20quisiera%20consultar%20por%20los%20cursos`"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-secondary text-cream font-display font-bold text-sm
                 shadow-lg shadow-secondary/20 hover:scale-105 active:scale-[0.97] transition-all duration-300"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
          </svg>
          Preguntar por los cursos
        </a>
      </div>
    </section>
    </Reveal>
  </AppLayout>
</template>
