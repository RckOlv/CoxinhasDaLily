<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const galleryImages = [
  { src: '/img/bolsacoxinhaspollo.jpg', alt: 'Bolsa de coxinhas de pollo' },
  { src: '/img/fuentecoxinhas.jpg', alt: 'Fuente de coxinhas servidas' },
  { src: '/img/bandejarissolis.jpg', alt: 'Bandeja de rissois' },
  { src: '/img/tortasalada.jpg', alt: 'Torta salada artesanal' },
  { src: '/img/mesasaladaevento.jpg', alt: 'Mesa preparada para evento' },
]

const videos = [
  { src: '/videos/produccioncoxinhas.mp4', title: 'Coxinhas' },
  { src: '/videos/produccionchurros.mp4', title: 'Churros' },
  { src: '/videos/bandejaminipizzas.mp4', title: 'Mini Pizzas' },
  { src: '/videos/masbandejasminipizzas.mp4', title: 'Bandejas' },
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

const selectedImage = ref(null)

function openImage(img) {
  selectedImage.value = img
  document.body.style.overflow = 'hidden'
}

function closeImage() {
  selectedImage.value = null
  document.body.style.overflow = ''
}

function handleKeydown(e) {
  if (e.key === 'Escape' && selectedImage.value) closeImage()
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
        <div class="hero-slide" style="background-image: url('/img/bolsacoxinhaspollo.jpg')" />
        <div class="hero-slide" style="background-image: url('/img/fuentecoxinhas.jpg')" />
        <div class="hero-slide" style="background-image: url('/img/bandejarissolis.jpg')" />
        <div class="hero-slide" style="background-image: url('/img/tortasalada.jpg')" />
        <div class="hero-slide" style="background-image: url('/img/mesasaladaevento.jpg')" />
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
            Ver Productos
          </Link>
          <a
            href="https://wa.me/5493758XXXXXX?text=Hola%20Lily,%20quisiera%20consultar%20por%20bandejas%20para%20un%20evento"
            target="_blank"
            rel="noopener noreferrer"
            class="py-4 px-8 rounded-2xl border-2 border-white/30 text-white font-display font-bold text-base
                   hover:bg-white/10 hover:scale-105 active:scale-[0.97] transition-all duration-300
                   flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            Solicitar para evento
          </a>
        </div>
      </div>
    </section>

    <!-- ¿Quiénes somos? -->
    <section class="bg-cream py-12 sm:py-16">
      <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
          <!-- Lado A: Foto -->
          <div class="flex justify-center animate-fade-in-up">
            <img
              src="/img/fotolily.jpg"
              alt="Lily - Fundadora de Coxinhas da Lily"
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

    <!-- Videos de producción -->
    <section class="bg-secondary py-12 sm:py-16">
      <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
        <h2 class="font-display font-bold text-xl sm:text-2xl text-white text-center mb-2 animate-fade-in-up">
          Detrás de escena
        </h2>
        <p class="text-white/60 text-sm text-center mb-8 animate-fade-in-up delay-100">
          Mirá cómo preparamos todo con amor
        </p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 max-sm:flex max-sm:overflow-x-auto max-sm:snap-x max-sm:snap-mandatory max-sm:pb-4 max-sm:-mx-5 max-sm:px-5">
          <div
            v-for="video in videos"
            :key="video.src"
            class="group rounded-2xl overflow-hidden bg-secondary-dark relative
                   transition-all duration-300 hover:shadow-xl animate-fade-in-up max-sm:min-w-[80%] max-sm:snap-center"
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

    <!-- Galería -->
    <section class="bg-cream-dark py-12 sm:py-16">
      <div class="max-w-5xl mx-auto px-5 sm:px-6 lg:px-8">
        <h2 class="font-display font-bold text-xl sm:text-2xl text-secondary text-center mb-6 animate-fade-in-up">
          Nuestros productos
        </h2>
        <div class="gallery-scroll">
          <div
            v-for="(img, i) in galleryImages"
            :key="img.src"
            class="rounded-2xl overflow-hidden border border-primary/10 shadow-sm cursor-pointer
                   transition-all duration-300 hover:scale-105 hover:shadow-md hover:border-primary/25 animate-fade-in-up"
            :style="{ animationDelay: `${i * 80}ms` }"
            @click="openImage(img)"
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

    <!-- Lightbox -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-200"
        leave-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
      >
        <div
          v-if="selectedImage"
          class="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 sm:p-8"
          @click.self="closeImage"
        >
          <button
            @click="closeImage"
            class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm
                   flex items-center justify-center text-white hover:bg-white/20 transition-colors z-10"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
          <img
            :src="selectedImage.src"
            :alt="selectedImage.alt"
            class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain animate-scale-in"
          />
        </div>
      </Transition>
    </Teleport>

    <!-- CTA Final -->
    <section class="bg-cream py-12 sm:py-16">
      <div class="max-w-lg mx-auto px-5 text-center">
        <h2 class="font-display font-bold text-xl sm:text-2xl text-secondary mb-2 animate-fade-in-up">
          ¿Te antojaste?
        </h2>
        <p class="text-secondary/60 text-sm mb-6 animate-fade-in-up delay-100">
          Mirá todo lo que tenemos preparado para vos.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 animate-fade-in-up delay-200">
          <Link
            href="/productos"
            class="flex-1 py-4 rounded-2xl bg-primary text-secondary font-display font-bold text-base
                   shadow-lg shadow-primary/25 hover:scale-105 active:scale-[0.97] transition-all duration-300
                   flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            Ver Productos
          </Link>
          <a
            href="https://wa.me/5493758XXXXXX?text=Hola%20Lily,%20quisiera%20consultar%20por%20bandejas%20para%20un%20evento"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1 py-4 rounded-2xl border-2 border-secondary/20 text-secondary font-display font-bold text-base
                   hover:bg-secondary/5 hover:scale-105 active:scale-[0.97] transition-all duration-300
                   flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            Solicitar para evento
          </a>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
