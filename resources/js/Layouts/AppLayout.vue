<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useCart } from '@/Composables/useCart'
import CartOffcanvas from '@/Components/CartOffcanvas.vue'
import PushSubscribe from '@/Components/PushSubscribe.vue'
import CookieConsent from '@/Components/CookieConsent.vue'
import Reveal from '@/Components/Reveal.vue'

const { itemCount } = useCart()
const showCart = ref(false)
const showAdminMore = ref(false)

const page = usePage()
const isHome = computed(() => page.url === '/')
const isAdmin = computed(() => page.url.startsWith('/admin'))
const isMoreActive = computed(() => page.url.startsWith('/admin/galeria') || page.url.startsWith('/admin/videos'))

const scrolled = ref(false)

function onScroll() {
  scrolled.value = window.scrollY > 50
}

function closeAdminMore() {
  showAdminMore.value = false
}

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('click', (e) => {
    if (showAdminMore.value && !e.target.closest('.relative')) {
      showAdminMore.value = false
    }
  })
  onScroll()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
})
</script>

<template>
  <div class="min-h-screen bg-cream">
    <!-- Header (hidden on admin pages) -->
    <header
      v-if="!isAdmin"
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
      :class="
        isHome
          ? (scrolled
              ? 'lg:bg-cream/95 lg:border-b lg:border-primary/10 lg:shadow-sm lg:backdrop-blur-sm bg-transparent'
              : 'bg-transparent')
          : 'bg-cream/95 border-b border-primary/10 shadow-sm backdrop-blur-sm'
      "
    >
      <!-- Desktop -->
      <div class="hidden lg:flex items-center justify-between h-16 px-4 sm:px-6 max-w-7xl mx-auto">
        <!-- Izquierda: vacío para equilibrar -->
        <div class="flex-1" />

        <!-- Centro: Nav -->
        <nav class="flex items-center gap-1">
          <Link
            href="/"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? ($page.url === '/' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
                    : ($page.url === '/' ? 'text-white bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/10'))
                : ($page.url === '/' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
            "
          >
            Inicio
          </Link>
          <Link
            href="/productos"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? ($page.url === '/productos' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
                    : ($page.url === '/productos' ? 'text-white bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/10'))
                : ($page.url === '/productos' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
            "
          >
            Catálogo
          </Link>
          <Link
            href="/eventos"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? ($page.url === '/eventos' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
                    : ($page.url === '/eventos' ? 'text-white bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/10'))
                : ($page.url === '/eventos' ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
            "
          >
            Eventos
          </Link>
          <a
            href="/#cursos"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? 'text-stone-400 hover:text-secondary hover:bg-primary/5'
                    : 'text-white/70 hover:text-white hover:bg-white/10')
                : 'text-stone-400 hover:text-secondary hover:bg-primary/5'
            "
          >
            Cursos
          </a>
          <Link
            v-if="$page.props.auth?.user"
            href="/admin"
            class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
            :class="
              isHome
                ? (scrolled
                    ? ($page.url.startsWith('/admin') ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
                    : ($page.url.startsWith('/admin') ? 'text-white bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/10'))
                : ($page.url.startsWith('/admin') ? 'bg-primary/15 text-secondary' : 'text-stone-400 hover:text-secondary hover:bg-primary/5')
            "
            translate="no"
          >
            Panel
          </Link>
        </nav>

        <!-- Derecha: Login + Carrito -->
        <div class="flex-1 flex items-center justify-end gap-1">
          <Link
            v-if="!$page.props.auth?.user"
            href="/login"
            class="hidden lg:flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold transition-colors"
            :class="isHome
              ? (scrolled ? 'text-secondary/50 hover:text-secondary hover:bg-primary/5' : 'text-white/50 hover:text-white hover:bg-white/10')
              : 'text-secondary/40 hover:text-secondary hover:bg-primary/5'"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
            Iniciar sesión
          </Link>
          <button
            @click="showCart = true"
            class="relative flex items-center justify-center w-10 h-10 rounded-xl transition-colors active:scale-95"
            :class="isHome
              ? (scrolled ? 'hover:bg-primary/10 text-secondary' : 'hover:bg-white/10 text-white')
              : 'hover:bg-primary/10 text-secondary'"
          >
            <svg
              class="w-6 h-6"
              :class="isHome
                ? (scrolled ? 'text-secondary' : 'text-white')
                : 'text-secondary'"
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            >
              <circle cx="9" cy="21" r="1" />
              <circle cx="20" cy="21" r="1" />
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
            </svg>
            <span
              v-if="itemCount > 0"
              class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[20px] h-5 px-1 text-[11px] font-bold text-secondary bg-primary rounded-full shadow-md shadow-primary/30 ring-2"
              :class="isHome ? (scrolled ? 'ring-cream' : 'ring-black/20') : 'ring-cream'"
            >
              {{ itemCount > 99 ? '99+' : itemCount }}
            </span>
          </button>
        </div>
      </div>

      <!-- Mobile: Logo centrado -->
      <div class="flex lg:hidden items-center justify-between h-16 px-4 sm:px-6">
        <div class="flex-1" />
        <Link v-if="!isHome" href="/" class="flex-1 flex justify-center">
          <img src="/img/logolily.png" alt="Coxinhas da Lily" class="h-12 w-auto" />
        </Link>
        <div v-else class="flex-1" />
        <div class="flex-1" />
      </div>
    </header>

    <!-- Contenido principal -->
    <main class="pb-24 lg:pb-8" :class="isHome || isAdmin ? '' : 'pt-16'">
      <slot />
    </main>

    <!-- Bottom Nav Bar Public (mobile) -->
    <nav v-if="!isAdmin" class="fixed bottom-0 left-0 right-0 z-50 bg-cream/95 border-t border-primary/10 shadow-[0_-4px_20px_rgba(120,53,15,0.06)] backdrop-blur-sm lg:hidden">
      <div class="flex items-center justify-around h-16 max-w-lg mx-auto">
        <Link
          href="/"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors"
          :class="$page.url === '/' ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" />
          </svg>
          Inicio
        </Link>
        <Link
          href="/productos"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors"
          :class="$page.url === '/productos' ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
          </svg>
          Catálogo
        </Link>
        <Link
          href="/eventos"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors"
          :class="$page.url === '/eventos' ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          Eventos
        </Link>
        <Link
          v-if="!$page.props.auth?.user"
          href="/login"
          class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
          :class="$page.url === '/login' ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
          Mi cuenta
        </Link>
        <Link
          v-else
          href="/admin"
          class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
          :class="$page.url.startsWith('/admin') ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
          translate="no"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
            <circle cx="12" cy="12" r="3" />
          </svg>
          Admin
        </Link>
        <button
          @click="showCart = true"
          class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs font-medium transition-colors relative"
          :class="showCart ? 'text-primary-dark' : 'text-stone-400 hover:text-secondary'"
        >
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
          </svg>
          <span
            v-if="itemCount > 0"
            class="absolute top-0 right-2 flex items-center justify-center min-w-[16px] h-4 px-1 text-[10px] font-bold text-secondary bg-primary rounded-full"
          >
            {{ itemCount > 99 ? '99+' : itemCount }}
          </span>
          Carrito
        </button>
      </div>
    </nav>

    <!-- Bottom Nav Bar Admin (mobile) -->
    <nav v-if="isAdmin" class="fixed bottom-0 left-0 right-0 z-50 bg-secondary border-t border-secondary/80 shadow-[0_-4px_20px_rgba(0,0,0,0.15)] lg:hidden">
      <div class="flex items-center justify-around h-16 max-w-lg mx-auto">
        <Link
          href="/admin/productos"
          class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
          :class="$page.url.startsWith('/admin/productos') ? 'text-primary' : 'text-white/50 hover:text-white'"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
          </svg>
          Productos
        </Link>
        <Link
          href="/admin/eventos"
          class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
          :class="$page.url.startsWith('/admin/eventos') ? 'text-primary' : 'text-white/50 hover:text-white'"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          Eventos
        </Link>
        <Link
          href="/admin/pedidos"
          class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
          :class="$page.url.startsWith('/admin/pedidos') ? 'text-primary' : 'text-white/50 hover:text-white'"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
            <rect x="9" y="3" width="6" height="4" rx="1" />
          </svg>
          Pedidos
        </Link>
        <Link
          href="/admin/categorias"
          class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
          :class="$page.url.startsWith('/admin/categorias') ? 'text-primary' : 'text-white/50 hover:text-white'"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="8" y1="6" x2="21" y2="6" /><line x1="8" y1="12" x2="21" y2="12" /><line x1="8" y1="18" x2="21" y2="18" />
            <line x1="3" y1="6" x2="3.01" y2="6" /><line x1="3" y1="12" x2="3.01" y2="12" /><line x1="3" y1="18" x2="3.01" y2="18" />
          </svg>
          Categorías
        </Link>
        <!-- Más (overflow) -->
        <div class="relative">
          <button
            @click="showAdminMore = !showAdminMore"
            class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors"
            :class="showAdminMore ? 'text-primary' : (isMoreActive ? 'text-primary' : 'text-white/50 hover:text-white')"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" />
            </svg>
            Más
          </button>
          <!-- Dropdown -->
          <Transition
            enter-active-class="transition-all duration-200"
            leave-active-class="transition-all duration-150"
            enter-from-class="opacity-0 translate-y-2 scale-95"
            leave-to-class="opacity-0 translate-y-2 scale-95"
          >
            <div
              v-if="showAdminMore"
              class="absolute bottom-full right-0 mb-2 w-44 bg-white rounded-2xl shadow-2xl border border-primary/10 overflow-hidden"
            >
              <Link
                href="/admin"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors"
                :class="$page.url === '/admin' ? 'bg-primary/10 text-secondary' : 'text-secondary/70 hover:bg-cream'"
                @click="showAdminMore = false"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Panel
              </Link>
              <Link
                href="/"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors border-t border-primary/5 text-secondary/70 hover:bg-cream"
                @click="showAdminMore = false"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="15" y1="9" x2="9.707" y2="15.293" /><line x1="9" y1="9" x2="15.293" y2="9.707" />
                </svg>
                Tienda
              </Link>
              <Link
                href="/admin/galeria"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors border-t border-primary/5"
                :class="$page.url.startsWith('/admin/galeria') ? 'bg-primary/10 text-secondary' : 'text-secondary/70 hover:bg-cream'"
                @click="showAdminMore = false"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                  <circle cx="8.5" cy="8.5" r="1.5" /><polyline points="21 15 16 10 5 21" />
                </svg>
                Galería
              </Link>
              <Link
                href="/admin/videos"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors border-t border-primary/5"
                :class="$page.url.startsWith('/admin/videos') ? 'bg-primary/10 text-secondary' : 'text-secondary/70 hover:bg-cream'"
                @click="showAdminMore = false"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
                Videos
              </Link>
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 hover:bg-red-50 transition-colors border-t border-primary/5"
                @click="showAdminMore = false"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Salir
              </Link>
            </div>
          </Transition>
        </div>
      </div>
    </nav>

    <!-- Footer -->
    <Reveal>
    <footer v-if="!isAdmin" class="bg-cream border-t border-primary/10 pb-20 lg:pb-0">
      <div class="max-w-5xl mx-auto px-5 py-10 sm:py-12">
        <!-- Logo centrado -->
        <div class="text-center mb-8">
          <Link href="/">
            <img src="/img/logolily.png" alt="Coxinhas da Lily" class="h-20 w-auto mx-auto" />
          </Link>
          <p class="text-sm text-secondary/50 leading-relaxed mt-3">
            Coxinhas y salgados brasileños artesanales con amor.
          </p>
        </div>

        <!-- Contacto + Redes -->
        <div class="max-w-xs mx-auto text-center">
            <h4 class="font-display font-bold text-secondary text-sm mb-3 uppercase tracking-wide">Contacto</h4>
            <ul class="space-y-2">
              <li class="flex items-center gap-2 justify-center">
                <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <span class="text-sm text-secondary/50">Posadas, Misiones</span>
              </li>
              <li>
                <a
                  href="https://www.instagram.com/Coxinas_da_lily"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center gap-2 text-sm text-secondary/50 hover:text-primary transition-colors justify-center"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" />
                    <circle cx="12" cy="12" r="5" />
                    <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none" />
                  </svg>
                  @Coxinas_da_lily
                </a>
              </li>
              <li>
                <a
                  href="https://www.tiktok.com/@lilianalovera681"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center gap-2 text-sm text-secondary/50 hover:text-primary transition-colors justify-center"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z" />
                  </svg>
                  Coxinhas da Lily
                </a>
              </li>
              <li>
                <a
                  href="https://wa.me/5493755300490"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center gap-2 text-sm text-secondary/50 hover:text-primary transition-colors justify-center"
                >
                  <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                  </svg>
                  WhatsApp
                </a>
              </li>
            </ul>
        </div>

        <!-- Links legales -->
        <div class="mt-8 pt-6 border-t border-primary/10 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-center">
          <Link href="/privacidad" class="text-xs text-secondary/40 hover:text-primary transition-colors">Política de Privacidad</Link>
          <Link href="/terminos" class="text-xs text-secondary/40 hover:text-primary transition-colors">Términos y Condiciones</Link>
          <Link href="/cookies" class="text-xs text-secondary/40 hover:text-primary transition-colors">Política de Cookies</Link>
        </div>

        <!-- Copyright -->
        <div class="mt-4 text-center">
          <p class="text-xs text-secondary/30">
            &copy; {{ new Date().getFullYear() }} Coxinhas da Lily. Todos los derechos reservados.
          </p>
        </div>
      </div>
    </footer>
    </Reveal>

    <!-- Botón flotante de WhatsApp -->
    <a
      v-if="!isAdmin"
      href="https://wa.me/5493755300490?text=Hola%20Lily%2C%20me%20gustar%C3%ADa%20hacer%20un%20pedido"
      target="_blank"
      rel="noopener noreferrer"
      class="fixed bottom-20 lg:bottom-6 right-4 z-50 w-14 h-14 rounded-full bg-[#25D366] text-white shadow-lg shadow-[#25D366]/30
             flex items-center justify-center transition-transform hover:scale-110 active:scale-95"
      aria-label="Contactar por WhatsApp"
    >
      <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
      </svg>
    </a>

    <!-- Cart Offcanvas -->
    <CartOffcanvas :open="showCart" @close="showCart = false" />

    <!-- Push Notifications -->
    <PushSubscribe />

    <!-- Consentimiento de cookies -->
    <CookieConsent v-if="!isAdmin" />
  </div>
</template>
